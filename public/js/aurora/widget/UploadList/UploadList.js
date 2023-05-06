define([
    "dojo/_base/declare",
    "dojo/parser",
    "dojo/ready",
    "dojo/_base/fx",
    "dojo/_base/lang",
    "dojo/mouse",
    "dojo/dom",
    "dojo/dom-construct",
    "dojo/on",
    "dijit/_WidgetBase",
    "dijit/_TemplatedMixin",
    "dojo/text!./template/UploadList.html"
], function(declare, parser, ready, baseFx, lang, mouse, dom, domConstruct, on, _WidgetBase, _TemplatedMixin, template) {
    declare("UploadList", [_WidgetBase, _TemplatedMixin], {
        templateString: template,
        fm: null,
        fileInput: "uploader",
        fileList: null,
        _uploaderSetter: function (value) {
            this.uploader = value;
        },
        _uploaderGetter: function() {
            return this.uploader;
        },
    });
    ready(function() {
        parser.parse();
    });
});