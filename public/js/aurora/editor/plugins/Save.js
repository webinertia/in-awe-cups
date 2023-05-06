define([
    "dojo",
    "dojo/query",
    "dojo/_base/array",
	"dojo/_base/lang",
    "dojo/topic",
    "dijit",
    "dijit/registry",
    "dojox/editor/plugins/Save",
    "dojox/validate/web"
], function(dojo, query, arrayUtil, lang, topic, dijit, registry, Save) {
    lang.extend(Save, {
        form: null,
        workSpace: null,
        _save: function() {
            // get the enclosing form
            this.form = registry.byId(this.editor.id).getParent();
            // not sure why we need this anymore
            this.workSpace = this.form.getParent();
            // form data object
            let formData   = new FormData(this.form.srcNodeRef);
            // set the correct value to the formData object
            // this is so that the editors content gets sent with the form
            formData.set(this.editor.name, this.editor.get("value"));
            // pass formData object to the save function
            this.save(formData);
        },
        onSuccess: function(resp, ioargs) {
           // let serverMessage = ;
            topic.publish('system/message', JSON.parse(resp));
            this.form.reset();
            this.editor.set('value', '');
        },
        onError: function(error) {
            //console.warn('save.js, onError callback resp', resp);
            switch(error.status) {
                // both of these response codes may send form errors back
                case 406:
                case 409:
                    if (error.response.data !== undefined) {
                        //alert('string found');
                        let resp = error.response.data;
                        if (typeof resp === 'string' && typeof resp !== 'object') {
                            resp = JSON.parse(resp);
                        }
                        // this expects the return of Dojo\Form $form->getMessages()
                        arrayUtil.forEach(resp, function(target) {
                            topic.publish('system/message', {message: target.message});
                        });
                    }
                    break;
            }
        },
        save: function(content) {
            if(!this.form.action) {
                topic.publish("system/message", {message: "The current form does not have an action defined, can not save!"});
            }
            if(this.form.isValid()){
                var postArgs = {
                    url: this.form.action,
                    postData: content
                };
                var deferred = dojo.xhrPost(postArgs);
                // hitch this parents onSuccess onError to the callback of the xhr
                deferred.addCallback(dojo.hitch(this, this.onSuccess));
                deferred.addErrback(dojo.hitch(this, this.onError));
            } else {
                this.form.validate();
                topic.publish("system/message", {message: "Invalid Data Supplied!!"});
            }
        }
    });
});