$(function() {
    var $collectionHolder;
    var sortable;

    // setup an "add a shift" link
    var linkText = Translator.trans('add_shift');
    var $addShiftLink = $('<a class="add_shift_link pull-right btn btn-secondary btn-sm" href="#"><i class="fa fa-plus" aria-hidden="true"></i></a>');
    var $newLinkLi = $('<div></div>').append($addShiftLink);

    // Get the ul that holds the collection of shifts
    $collectionHolder = $('ul.shifts');

    // add the "add a shift" anchor and li to the shifts ul
    $collectionHolder.append($newLinkLi);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addShiftLink.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // add a new shift form (see next code block)
        addShiftForm($collectionHolder, $newLinkLi);
        addDatePicker();
        addCross();
        addDragFunctionality();
        sortable.sortable( "refresh" );
        setOrderIndexes();
    });

    $('.shifts > li').each(function (key, el) {
      $(el).prepend(cross);
    });

    $('.close').each(function (key, el) {
        $(el).click(function (e) {
            $(this).parent().parent().remove();
        });
    });

    sortable = $('.shifts').sortable({
        handle: '.drag-handle',
        scrollSpeed: 10,
        cursorAt: { top: 0, right: 0 },
        stop: function( event, ui ) {
            setOrderIndexes();
        }
    });

    addDragFunctionality();
});

function addShiftForm($collectionHolder, $newLinkLi) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');

    // get the new index
    var index = $collectionHolder.data('index');

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    var newForm = prototype.replace(/__name__/g, index);

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a shift" link li
    var $newFormLi = $('<li class="animated fadeIn card"></li>').append(newForm);
    $newLinkLi.before($newFormLi);
}

function addDatePicker() {
    $('.datepicker').each(function (index, value) {
        $(value).datepicker();
    });
}

var cross = '<div class="d-flex justify-content-end"><i class="fa close fa-times text-danger" aria-hidden="true"></i><i class="fa fa-bars drag-handle" aria-hidden="true"></i></div>';
function addCross(){
    $('.shifts > li').last().prepend(cross);
    $('.close').click(function (e) {
        $(this).parent().parent().remove();
    });
}

function addDragFunctionality() {
    $('.drag-handle').mousedown(function (el) {
        startDrag();
    });

    $(document).mouseup(function (el) {
        endDrag();
    });
}

function setOrderIndexes() {
    $('.order-index').each(function (index, value) {
        $(value).attr('value', index);
    });
}

function startDrag() {
    $('.shifts li > div:nth-child(2)').each(function (key, el) {
        $(el).hide();
        $('.fa-times').hide();
        var title = $(el).find('input').first().val();
        if(title) {
            $(el).parent().append('<span class="index-label">'+title+'</span>');
        } else {
            // make sure user won't be confused
            var keyToShow = key + 1;
            $(el).parent().append('<span class="index-label">Plan: '+ keyToShow +'</span>');
        }
    })
}

function endDrag() {
    $('.shifts li > div:nth-child(2)').each(function (key, el) {
        $(el).show();
        $('.fa-times').show();
        $(el).addClass('animated').addClass('fadeIn');
        $(el).parent().find('.index-label').remove()
    });
}