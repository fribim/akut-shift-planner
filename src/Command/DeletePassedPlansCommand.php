<?php

namespace App\Command;

use App\Entity\Plan;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Twig\Environment;
use Symfony\Component\Console\Input\InputOption;

class DeletePassedPlansCommand extends Command
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;


    /**
     * @var \Twig_Environment
     */
    private $templating;

    /**
     * DeletePassedPlansCommand constructor.
     * @param EntityManagerInterface $em
     * @param \Swift_Mailer $mailer
     * @param \Twig_Environment $templating
     */
    public function __construct(
        EntityManagerInterface $em,
        \Swift_Mailer $mailer,
        \Twig_Environment $templating
    ) {
        parent::__construct();
        $this->em = $em;
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    /**
     * Configure Command
     */
    protected function configure()
    {
        $this
            ->setName('app:delete-passed-plans')
            ->setDescription('Delete passed plans')
            ->setHelp('Delete all plans and collections that are behind a certain time range (default 30 days)')
            ->addOption(
                'dueDays',
                null,
                InputOption::VALUE_REQUIRED,
                'Specify the number of days a plan has to pass its due date, to be deleted',
                30
            );
    }

    /**
     * Run it
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return  void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $qb = $this->em->getRepository('App:Plan')->createQueryBuilder('p');
        $days =  intval($input->getOption('dueDays'));
        if ($days < 0) {
            $days = 0;
        }

        $dueDate = new \DateTime();
        $dueDate->sub(new \DateInterval('P' . $days . 'D'));

        $passedPlans = $qb->where('p.date <= :dueDate')
            ->setParameter('dueDate', $dueDate)
            ->getQuery()
            ->getResult();

        if ($passedPlans) {
            try {
                $this->deleteEachPlan($passedPlans);
                $this->deleteEachEmptyCollection();
                $output->write('<info>Plans deleted: ' . count($passedPlans) . '</info>');
            } catch(\Exception $e){
                $output->write('<error>Deletion failed. Sent error via email to admin: ' . $e->getMessage() . '</error>');
                $this->sendFailedEmail($e->getMessage());
            }
        } else {
            $output->write('<info>Nothing to delete</info>');
        }
        return 0;
    }


    /**
     * @param $plans
     */
    private function deleteEachPlan($plans)
    {
        foreach ($plans as $plan) {
            $this->em->remove($plan);
        }
        $this->em->flush();
    }

    /**
     * remove all empty collections
     */
    private function deleteEachEmptyCollection() {
        $qb = $this->em->createQueryBuilder();

        $q = $qb->select('c')
            ->from('App:PlanCollection', 'c')
            ->leftJoin('c.plans','p')
            ->having('COUNT(p.id) = 0')
            ->groupBy('c.id')
            ->getQuery();

        $emptyCollections = $q->getResult();

        foreach ($emptyCollections as $collection) {
            $this->em->remove($collection);
        }

        $this->em->flush();
    }

    /**
     * Send an email with the error output to the admin
     */
    private function sendFailedEmail($error)
    {
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('no-reply@schicht-plan.ch')
            ->setTo('admin@schicht-plan.ch')
            ->setBody(
                $this->templating->render(
                    'email/plan-deletion-failed.txt.twig',
                    array(
                        'error' => $error
                    )
                ),
                'text/plain'
            );

        $this->mailer->send($message);
    }
}