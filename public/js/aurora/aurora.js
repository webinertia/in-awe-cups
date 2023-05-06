define([
    "dojo/Stateful",
    "dojo/_base/declare",
    "dojo/topic",
    "dojo/_base/Deferred",
    "dojo/request/notify",
    "dojo/dom",
    "dojo/query",
    "dojo/_base/array",
    "dojo/dom-construct"
],
function(Stateful, declare, topic, Deferred, notify, dom, query, arrayUtil, domConstruct) {
    // create Aurora class, as subclass of Stateful
        return declare("Aurora", [Stateful], {
        errorClass: "invalidValue",
        successClass: "validValue",
        serviceManager: null, // this is a registry
        formManager: null, // this is the formManager that will be set
        message: null,
        adminMessage: null,
        // _messageSetter: function(value) {
        //     this.
        // },
        ajaxify: function(event) {
            event.preventDefault();
            event.stopPropagation();
        },
        /** var string formName */
        getSaveButtonId: function(formName) {
            if (typeof formName === 'string' || formName instanceof String) {
                var buttonId = "save" + formName + "Button";
                return buttonId;
            }
            return null;
        },
        dashToSeperator: function(str) {
            if(this.isString(str)) {
                return str.replace(/[_-]/g, " ");
            }
        },
        isString: function(str) {
            return typeof str === 'string' || str instanceof String;
        },
        sendUiMessage: function (dialog, ) {// currently not in use
            dialog.set("content", this.message);
            dialog.show();
        },
        addChildToDijit(parent, childToAdd) {
            parent.addChild(childToAdd);
        }
    });
});