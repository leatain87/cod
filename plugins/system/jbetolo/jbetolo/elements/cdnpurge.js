/**
* @version:	2.6.1.a2a4257 - 2014 September 15 10:13:22 +0300
* @package:	jbetolo
* @subpackage:	jbetolo
* @copyright:	Copyright (C) 2010 - 2014 jproven.com. All rights reserved. 
* @license:	GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
*/

var jbetolocdnpurge = new Class({
        Implements: [Options],
        
        initialize: function(options) {
                this.setOptions(options);

                window.addEvent('domready', function(){
                        this.assignActions();
                }.bind(this));
        },

        assignActions: function() {
                document.id('cdnpurgeBtn').addEvent('click', function() { this.cdnpurge(); }.bind(this));
        },

        /** field/param element actions **/

        cdnpurge: function(){
                var request = this.options.base+'index.php?option=com_jbetolo&task=cdnpurge';
                request += 
                        '&keys='+$('cdnpurgeKeys').get('value') + 
                        '&purge='+$('cdnpurgePurge').get('value') + 
                        '&cdn='+$('cdnpurgeCDN').get('value')
                ;

                new Request({
                        onSuccess: function(response) {
                                alert(response);
                        }.bind(this),
                        url: request,
                        noCache: true
                }).send();
                
                return false;
        }
});