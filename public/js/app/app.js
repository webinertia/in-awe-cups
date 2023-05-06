define([
    "dojo/Stateful",
    "dojo/_base/declare",
    "dojo/topic",
    "dojo/_base/Deferred",
    "dojo/request",
    "dojo/request/notify",
    "dojo/dom",
    "dojo/dom-class",
    "dojo/query",
    "dojo/_base/array",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/_base/lang",
    "dojo/has",
    "dojo/domReady!"
],
function(Stateful, declare, topic, Deferred, request, notify, dom, domClass, query, arrayUtil, domConstruct, on, lang, has) {
    // create App class, as subclass of Stateful
    return declare("App", [Stateful], {
        init: () => {
            topic.subscribe("system/message", e => {
                let messageClass = 'alert-' + e.type;
                domClass.add('sys-message-alert', messageClass);
                dom.byId('sys-message-alert').innerHTML = e.message;
                const messenger = new bootstrap.Modal('#system-message-modal');
                messenger.toggle();
            });
            // if we have an XMLHttpRequest capable browser, lets us ajax
            if (has("native-xhr")) {
                let menuHandler = {
                    id: "menuHandler",
                    onClick: evt => {
                        //evt.preventDefault();
                        //
                        topic.publish("system/message", {message: "Test message to test the alert system", type: "danger"});
                    }
                };
                let mainNav = dom.byId("main-nav");
                on(mainNav, ".nav-link:click", lang.hitch(menuHandler, "onClick"));
            }
        }
    });
});