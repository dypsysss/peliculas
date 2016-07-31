/**
 * Created by carless on 14/05/16.
 * @package		Joomla
 * @subpackage	Peliculas
 * @copyright	Copyright (C) 2005 - 2014 CESI Informàtica i comunicions. All rights reserved.
 * @license		Comercial License
 */

var Peliculas = Peliculas || {};
Peliculas.eMovies = {};

Peliculas.eMovies.App = new Class({
    Implements: [Events, Options],
    options: {
        selectorTable: 'eventsMoviesTable',
        fieldCinemaID: 'jform_cinema_id'
    },
    tableItems: null,

    initialize: function(options) {
        this.setOptions(options);
        this.tableItems = new HtmlTable(document.id(this.options.selectorTable));
        jQuery('.eventMovieDelete').bind('click', this.deleteItem);
    },

    deleteItem: function(e) {
        var event = e || window.event;
        if (event.stop) {
            event.stop();
        }
        event.preventDefault();

        var row = event.target.getParent().getParent();
        var num = event.target.getParent().getParent().getAttribute('rel');

        var IDDelete     = 'emovie_' + num + '_deleted';

        if (jQuery("#" + IDDelete)) {
            jQuery("#" + IDDelete).val(1);
        }
        row.setStyle('display', 'none');
    },

    appendItem: function() {

        var cinemaID = -1;
        if (jQuery("#" + this.options.fieldCinemaID)) {
            cinemaID = jQuery("#" + this.options.fieldCinemaID).val();
        }

        var numOfItems = this.tableItems.body.rows.length;
        numOfItems = numOfItems + 1;

        var itemRow = new Element('tr');
        itemRow.setAttribute('rel', numOfItems);

        var InputID = new Element('input');
        InputID.setAttribute('name', 'jform[emovie]['+numOfItems+'][id]');
        InputID.setAttribute('id', 'emovie_'+numOfItems+'_id');
        InputID.setAttribute('type', 'hidden');
        InputID.setAttribute('value', '-1');

        var DeletedID = new Element('input');
        DeletedID.setAttribute('name', 'jform[emovie]['+numOfItems+'][deleted]');
        DeletedID.setAttribute('id', 'emovie_'+numOfItems+'_deleted');
        DeletedID.setAttribute('type', 'hidden');

        var InputCinemaRoom = new Element('div');
        InputCinemaRoom.setAttribute('class', 'input-append');
        InputCinemaRoom.innerHTML = '<input type="text" id="dsp_event_'+numOfItems+'_cinemaroom_id" value="" readonly>' +
                '<a rel="{handler: \'iframe\', size: {x: 800, y: 500}}" href="index.php?option=com_peliculas&amp;view=cinemasrooms&amp;layout=modal&amp;tmpl=component&amp;field=modalCinemaRoomReturn&amp;filter_cinemaid='+cinemaID+'&amp;eventid='+numOfItems+'" title="Seleccionar" class="btn btn-primary modal_'+numOfItems+'_cinemaroom_id">' +
                '<i class="icon-search"></i></a>'+
                '</div>' +
                '<input type="hidden" value="0" name="jform[emovie]['+numOfItems+'][cinemaroom_id]" id="event_'+numOfItems+'_cinemaroom_id">';

        var TDIDCell = new Element('td');
        TDIDCell.setAttribute('class', 'left');
        TDIDCell.appendChild(InputID);
        TDIDCell.appendChild(DeletedID);
        TDIDCell.appendChild(InputCinemaRoom);

        var InputMovie = new Element('div');
        InputMovie.setAttribute('class', 'input-append');
        InputMovie.innerHTML = '<input type="text" id="dsp_emovie_'+numOfItems+'_movie_id" value="" readonly>' +
            '<a rel="{handler: \'iframe\', size: {x: 800, y: 500}}" href="index.php?option=com_peliculas&amp;view=movies&amp;layout=modal&amp;tmpl=component&amp;field=modalMovieReturn&amp;emovieid='+numOfItems+'" title="Seleccionar" class="btn btn-primary modal_'+numOfItems+'_movie_id">' +
            '<i class="icon-search"></i></a>'+
            '</div>' +
            '<input type="hidden" value="0" name="jform[emovie]['+numOfItems+'][movie_id]" id="emovie_'+numOfItems+'_movie_id">';

        var TDMovieCell = new Element('td');
        TDMovieCell.setAttribute('class', 'left');
        TDMovieCell.appendChild(InputMovie);


        var InputInfo = new Element('textarea');
        InputInfo.setAttribute('name', 'jform[emovie]['+numOfItems+'][info]');
        InputInfo.setAttribute('id', 'emovie_'+numOfItems+'_info');
        InputInfo.setAttribute('rows', '3');
        InputInfo.setAttribute('cols', '30');
        InputInfo.setAttribute('class', 'span12');
        // InputInfo.setAttribute('type', 'hidden');
        InputInfo.setAttribute('value', '');

        var TDInfoCell = new Element('td');
        TDInfoCell.setAttribute('class', 'left');
        TDInfoCell.appendChild(InputInfo);

        var TDActions = new Element('td');
        var btnDelete = new Element('a');
        btnDelete.setAttribute('class', 'btn eventMovieDelete');
        btnDelete.setAttribute('href', '#');
        btnDelete.setAttribute('id', 'btn_emovie_'+numOfItems+'_deleted');
        btnDelete.innerHTML='<span class="icon-delete"></span> Borrar';

        TDActions.appendChild(btnDelete);

        itemRow.appendChild(TDIDCell);
        itemRow.appendChild(TDMovieCell);
        itemRow.appendChild(TDInfoCell);
        itemRow.appendChild(TDActions);

        var tbody = document.id(this.options.selectorTable).getElementsByTagName('tbody')[0];
        tbody.appendChild(itemRow);

        jQuery('#btn_emovie_'+numOfItems+'_deleted').bind('click', this.deleteItem);

        SqueezeBox.initialize({});
        SqueezeBox.assign(jQuery('a.modal_'+numOfItems+'_cinemaroom_id').get(), {
            parse: 'rel'
        });
        SqueezeBox.assign(jQuery('a.modal_'+numOfItems+'_movie_id').get(), {
            parse: 'rel'
        });
    },

    modalReturn: function(id, title) {

    }
});

function jModalCinemasRooms_modalCinemaRoomReturn(eventId, id, title) {

    var dspField = 'dsp_event_'+eventId+'_cinemaroom_id';
    var idField = 'event_'+eventId+'_cinemaroom_id';

    jQuery("#" + dspField).val(title);
    jQuery("#" + idField).val(id);

    SqueezeBox.close();
}

function jModalMovies_modalMovieReturn(eMovieId, id, title) {

    var dspField = 'dsp_emovie_'+eMovieId+'_movie_id';
    var idField = 'emovie_'+eMovieId+'_movie_id';

    jQuery("#" + dspField).val(title);
    jQuery("#" + idField).val(id);

    SqueezeBox.close();
}