define([
    "dojo",
	"dojo/_base/lang",
    "dojo/topic",
    "dijit",
    "dijit/form/Button",
    "dijit/form/_ButtonMixin"
], function(dojo, lang, topic, dijit, Button) {
    lang.extend(Button, {
        form: null,
        workSpace: null,
        onClick: function(e) {
            this.form = dijit.getEnclosingWidget(this);
            this.form.reset();
            return false;
        }
    });
});