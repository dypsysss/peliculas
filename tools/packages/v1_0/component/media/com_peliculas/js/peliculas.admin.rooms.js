/**
 * Created by carless on 14/05/16.
 * @package		Joomla
 * @subpackage	Peliculas
 * @copyright	Copyright (C) 2005 - 2014 CESI Informàtica i comunicions. All rights reserved.
 * @license		Comercial License
 */

var Peliculas = Peliculas || {};
Peliculas.rooms = {};

Peliculas.rooms.App = new Class({
    Implements: [Events, Options],
    options: {
        selectorTable: 'cinemaRoomsTable'
    },
    tableRooms: null,
    
    initialize: function(options) {
        this.setOptions(options);
        this.tableRooms = new HtmlTable(document.id(this.options.selectorTable));
        jQuery('.cinemaRoomDelete').bind('click', this.deleteItem);
    },

    deleteItem: function(e) {
        var event = e || window.event;
        if (event.stop) {
            event.stop();
        }
        event.preventDefault();

        var row = event.target.getParent().getParent();
        var num = event.target.getParent().getParent().getAttribute('rel');

        var IDDelete     = 'CinemaRoom_' + num + '_deleted';

        if (jQuery("#" + IDDelete)) {
            jQuery("#" + IDDelete).val(1);
        }
        row.setStyle('display', 'none');
    },

    appendItem: function() {

        var numOfItems = this.tableRooms.body.rows.length;
        numOfItems = numOfItems + 1;

        var itemRow = new Element('tr');
        itemRow.setAttribute('rel', numOfItems);

        var InputID = new Element('input');
        InputID.setAttribute('name', 'jform[cinemaroom]['+numOfItems+'][id]');
        InputID.setAttribute('id', 'CinemaRoom_'+numOfItems+'_id');
        InputID.setAttribute('type', 'hidden');
        InputID.setAttribute('value', '-1');

        var DeletedID = new Element('input');
        DeletedID.setAttribute('name', 'jform[cinemaroom]['+numOfItems+'][deleted]');
        DeletedID.setAttribute('id', 'CinemaRoom_'+numOfItems+'_deleted');
        DeletedID.setAttribute('type', 'hidden');

        var InputName = new Element('input');
        InputName.setAttribute('name', 'jform[cinemaroom]['+numOfItems+'][name]');
        InputName.setAttribute('id', 'CinemaRoom_'+numOfItems+'_name');
        InputName.setAttribute('class', 'inputbox');
        InputName.setAttribute('type', 'text');
        InputName.setAttribute('value', '');

        var TDIDCell = new Element('td');
        TDIDCell.setAttribute('class', 'left');
        TDIDCell.appendChild(InputID);
        TDIDCell.appendChild(DeletedID);
        TDIDCell.appendChild(InputName);

        var TDActions = new Element('td');
        var btnDelete = new Element('a');
        btnDelete.setAttribute('class', 'btn cinemaRoomDelete');
        btnDelete.setAttribute('href', '#');
        btnDelete.setAttribute('id', 'btn_room_'+numOfItems+'_deleted');
        btnDelete.innerHTML='<span class="icon-delete"></span> Borrar';

        TDActions.appendChild(btnDelete);

        itemRow.appendChild(TDIDCell);
        itemRow.appendChild(TDActions);

        var tbody = document.id(this.options.selectorTable).getElementsByTagName('tbody')[0];
        tbody.appendChild(itemRow);

        jQuery('#btn_room_'+numOfItems+'_deleted').bind('click', this.deleteItem);
    }

});