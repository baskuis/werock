;(function(window, document) {


    /**
     * Filereader polyfill
     *
     *
     */
    (function(window, document){

        if(window.FileReader !== undefined) return null;

        /**
         * Flash FileReader Proxy
         */
        window.FileAPIProxy = {
            ready: false,
            init: function(o) {

                var self = this;

                this.debugMode = o.debugMode;

                this.container = $('<div>').attr('id', o.id)
                    .wrap('<div>')
                    .parent()
                    .css({
                        position:'fixed',
                        // top:'0px',
                        width:'1px',
                        height:'1px',
                        display:'inline-block',
                        background:'transparent',
                        'z-index':99999
                    })
                    // Hands over mouse events to original input for css styles
                    .on('mouseover mouseout mousedown mouseup', function(evt) {
                        if(currentTarget) $('#' + currentTarget).trigger(evt.type);
                    })
                    .appendTo('body');

                try {
					
					//for old ios
					if(typeof swfobject === 'undefined') return;
					
					
                    swfobject.embedSWF(o.filereader, o.id, '100%', '100%', '10', o.expressInstall, {debugMode: o.debugMode ? true : ''}, {'wmode':'transparent','allowScriptAccess':'always'}, {id: o.id}, function(e) {

                        self.swfObject = e.ref;
                        $(self.swfObject).css({display: 'block', outline: 0 }).attr('tabindex', 0);

                        if (self.ready) {
                            readyCallbacks.fire();
                        }

                        self.ready = e.success && typeof e.ref.add === "function";

                    });

                } catch(e){
                    console.log(e);
                }

            },
            swfObject: null,
            container: null,
            // Inputs Registry
            inputs: {},
            // Readers Registry
            readers: {},
            // Receives FileInput events
            onFileInputEvent: function(evt) {
                if (this.debugMode) console.info('FileInput Event ', evt.type, evt);
                if (evt.target in this.inputs) {
                    var el = this.inputs[evt.target];
                    evt.target = el[0];
                    if( evt.type === 'change') {
                        evt.files = new FileList(evt.files);
                        evt.target = {files: evt.files};
                    }
                    el.trigger(evt);
                }
                window.focus();
            },
            // Receives FileReader ProgressEvents
            onFileReaderEvent: function(evt) {
                if (this.debugMode) console.info('FileReader Event ', evt.type, evt, evt.target in this.readers);
                if (evt.target in this.readers) {
                    var reader = this.readers[evt.target];
                    evt.target = reader;
                    reader._handleFlashEvent.call(reader, evt);
                }
            },
            // Receives flash FileReader Error Events
            onFileReaderError: function(error) {
                if (this.debugMode) console.log(error);
            },
            onSWFReady: function() {
                this.container.css({position: 'absolute'});
                this.ready = typeof this.swfObject.add === "function";
                if (this.ready) {
                    readyCallbacks.fire();
                }

                return true;
            }
        };

        /**
         * Add FileReader to the window object
         */
        window.FileReader = function () {

            //mark as polyfill
            this.POLYFILL = true;

            // states
            this.EMPTY = 0;
            this.LOADING = 1;
            this.DONE = 2;

            this.readyState = 0;

            // File or Blob data
            this.result = null;

            this.error = null;

            // event handler attributes
            this.onloadstart = null;
            this.onprogress = null;
            this.onload = null;
            this.onabort = null;
            this.onerror = null;
            this.onloadend = null;

            // Event Listeners handling using JQuery Callbacks
            this._callbacks = {
                loadstart : $.Callbacks( "unique" ),
                progress  : $.Callbacks( "unique" ),
                abort     : $.Callbacks( "unique" ),
                error     : $.Callbacks( "unique" ),
                load      : $.Callbacks( "unique" ),
                loadend   : $.Callbacks( "unique" )
            };

            // Custom properties
            this._id = null;

            //return
            return this;

        };

        window.FileReader.prototype = {
            // async read methods
            readAsBinaryString: function (file) {
                this._start(file);
                FileAPIProxy.swfObject.read(file.input, file.name, 'readAsBinaryString');
            },
            readAsText: function (file, encoding) {
                this._start(file);
                FileAPIProxy.swfObject.read(file.input, file.name, 'readAsText');
            },
            readAsDataURL: function (file) {
                this._start(file);
                FileAPIProxy.swfObject.read(file.input, file.name, 'readAsDataURL');
            },
            readAsArrayBuffer: function(file){
                throw("Whoops FileReader.readAsArrayBuffer is unimplemented");
            },

            abort: function () {
                this.result = null;
                if (this.readyState === this.EMPTY || this.readyState === this.DONE) return;
                FileAPIProxy.swfObject.abort(this._id);
            },

            // Event Target interface
            addEventListener: function (type, listener) {
                if (type in this._callbacks) this._callbacks[type].add(listener);
            },
            removeEventListener: function (type, listener) {
                if (type in this._callbacks) this._callbacks[type].remove(listener);
            },
            dispatchEvent: function (event) {
                event.target = this;
                if (event.type in this._callbacks) {
                    var fn = this['on' + event.type];
                    if ($.isFunction(fn)) fn(event);
                    this._callbacks[event.type].fire(event);
                }
                return true;
            },

            // Custom private methods

            // Registers FileReader instance for flash callbacks
            _register: function(file) {
                this._id = file.input + '.' + file.name;
                FileAPIProxy.readers[this._id] = this;
            },
            _start: function(file) {
                this._register(file);
                if (this.readyState === this.LOADING) throw {type: 'InvalidStateError', code: 11, message: 'The object is in an invalid state.'};
            },
            _handleFlashEvent: function(evt) {
                switch (evt.type) {
                    case 'loadstart':
                        this.readyState = this.LOADING;
                        break;
                    case 'loadend':
                        this.readyState = this.DONE;
                        break;
                    case 'load':
                        this.readyState = this.DONE;
                        this.result = FileAPIProxy.swfObject.result(this._id);
                        break;
                    case 'error':
                        this.result = null;
                        this.error = {
                            name: 'NotReadableError',
                            message: 'The File cannot be read!'
                        };
                }
                this.dispatchEvent(new FileReaderEvent(evt));
            }
        };

        /**
         * FileReader ProgressEvent implenting Event interface
         */
        FileReaderEvent = function (e) {
            this.initEvent(e);
        };

        FileReaderEvent.prototype = {
            initEvent: function (event) {
                $.extend(this, {
                    type: null,
                    target: null,
                    currentTarget: null,

                    eventPhase: 2,

                    bubbles: false,
                    cancelable: false,

                    defaultPrevented: false,

                    isTrusted: false,
                    timeStamp: new Date().getTime()
                }, event);
            },
            stopPropagation: function (){
            },
            stopImmediatePropagation: function (){
            },
            preventDefault: function (){
            }
        };

        /**
         * FileList interface (Object with item function)
         */
        FileList = function(array) {
            if (array) {
                for (var i = 0; i < array.length; i++) {
                    this[i] = array[i];
                }
                this.length = array.length;
            } else {
                this.length = 0;
            }
        };

        FileList.prototype = {
            item: function(index) {
                if (index in this) return this[index];
                return null;
            }
        };

        /**
         * Configure flash based filereader
         */
        if (!FileAPIProxy.ready) {
            FileAPIProxy.init({
                id              : 'fileReaderSWFObject', // ID for the created swf object container,
                multiple        : null,
                accept          : null,
                label           : null,
                extensions      : null,
                filereader      : '/modules/Media/assets/filereader.swf', // The path to the filereader swf file
                expressInstall  : null, // The path to the express install swf file
                debugMode       : true,
                callback        : false // Callback function when Filereader is ready
            });
        }

    })(window, document);

    /**
     * END Filereader polyfill
     *
     *
     */





    var FileReader = window.FileReader;
    var FileReaderSyncSupport = false;
    var workerScript = "self.addEventListener('message', function(e) { var data=e.data; try { var reader = new FileReaderSync; postMessage({ result: reader[data.readAs](data.file), extra: data.extra, file: data.file})} catch(e){ postMessage({ result:'error', extra:data.extra, file:data.file}); } }, false);";
    var syncDetectionScript = "onmessage = function(e) { postMessage(!!FileReaderSync); };";
    var fileReaderEvents = ['loadstart', 'progress', 'load', 'abort', 'error', 'loadend'];
    var sync = false;
    var FileReaderJS = window.FileReaderJS = {
        enabled: false,
        setupInput: setupInput,
        setupDrop: setupDrop,
        setupClipboard: setupClipboard,
        setSync: function (value) {
            sync = value;
            if (sync && !FileReaderSyncSupport) {
                checkFileReaderSyncSupport();
            }
        },
        getSync: function() {
            return sync && FileReaderSyncSupport;
        },
        output: [],
        opts: {
            dragClass: "drag",
            accept: false,
            readAsDefault: 'DataURL',
            readAsMap: {
            },
            on: {
                loadstart: noop,
                progress: noop,
                load: noop,
                abort: noop,
                error: noop,
                loadend: noop,
                skip: noop,
                groupstart: noop,
                groupend: noop,
                beforestart: noop
            }
        }
    };

    // Setup jQuery plugin (if available)
    if (typeof(jQuery) !== "undefined") {
        jQuery.fn.fileReaderJS = function(opts) {
            return this.each(function() {
                if (jQuery(this).is("input")) {
                    setupInput(this, opts);
                }
                else {
                    setupDrop(this, opts);
                }
            });
        };

        jQuery.fn.fileClipboard = function(opts) {
            return this.each(function() {
                setupClipboard(this, opts);
            });
        };
    }

    // Not all browsers support the FileReader interface. Return with the enabled bit = false.
    if (!FileReader) {
        return;
    }


    // makeWorker is a little wrapper for generating web workers from strings
    function makeWorker(script) {
        var URL = window.URL || window.webkitURL;
        var Blob = window.Blob;
        var Worker = window.Worker;

        if (!URL || !Blob || !Worker || !script) {
            return null;
        }

        var blob = new Blob([script]);
        var worker = new Worker(URL.createObjectURL(blob));
        return worker;
    }

    // setupClipboard: bind to clipboard events (intended for document.body)
    function setupClipboard(element, opts) {

        if (!FileReaderJS.enabled) {
            return;
        }
        var instanceOptions = extend(extend({}, FileReaderJS.opts), opts);

        element.addEventListener("paste", onpaste, false);

        function onpaste(e) {
            var files = [];
            var clipboardData = e.clipboardData || {};
            var items = clipboardData.items || [];

            for (var i = 0; i < items.length; i++) {
                var file = items[i].getAsFile();

                if (file) {

                    // Create a fake file name for images from clipboard, since this data doesn't get sent
                    var matches = new RegExp("/\(.*\)").exec(file.type);
                    if (!file.name && matches) {
                        var extension = matches[1];
                        file.name = "clipboard" + i + "." + extension;
                    }

                    files.push(file);
                }
            }

            if (files.length) {
                processFileList(e, files, instanceOptions);
                e.preventDefault();
                e.stopPropagation();
            }
        }
    }

    // setupInput: bind the 'change' event to an input[type=file]
    function setupInput(input, opts) {

        if (!FileReaderJS.enabled) {
            return;
        }
        var instanceOptions = extend(extend({}, FileReaderJS.opts), opts);

        input.addEventListener("change", inputChange, false);
        input.addEventListener("drop", inputDrop, false);

        function inputChange(e) {
            processFileList(e, input.files, instanceOptions);
        }

        function inputDrop(e) {
            e.stopPropagation();
            e.preventDefault();
            processFileList(e, e.dataTransfer.files, instanceOptions);
        }
    }

    // setupDrop: bind the 'drop' event for a DOM element
    function setupDrop(dropbox, opts) {

        if (!FileReaderJS.enabled) {
            return;
        }
        var instanceOptions = extend(extend({}, FileReaderJS.opts), opts);
        var dragClass = instanceOptions.dragClass;
        var initializedOnBody = false;

        // Bind drag events to the dropbox to add the class while dragging, and accept the drop data transfer.
        dropbox.addEventListener("dragenter", onlyWithFiles(dragenter), false);
        dropbox.addEventListener("dragleave", onlyWithFiles(dragleave), false);
        dropbox.addEventListener("dragover", onlyWithFiles(dragover), false);
        dropbox.addEventListener("drop", onlyWithFiles(drop), false);

        // Bind to body to prevent the dropbox events from firing when it was initialized on the page.
        document.body.addEventListener("dragstart", bodydragstart, true);
        document.body.addEventListener("dragend", bodydragend, true);
        document.body.addEventListener("drop", bodydrop, false);

        function bodydragend(e) {
            initializedOnBody = false;
        }

        function bodydragstart(e) {
            initializedOnBody = true;
        }

        function bodydrop(e) {
            if (e.dataTransfer.files && e.dataTransfer.files.length ){
                e.stopPropagation();
                e.preventDefault();
            }
        }

        function onlyWithFiles(fn) {
            return function() {
                if (!initializedOnBody) {
                    fn.apply(this, arguments);
                }
            };
        }

        function drop(e) {
            e.stopPropagation();
            e.preventDefault();
            if (dragClass) {
                removeClass(dropbox, dragClass);
            }
            processFileList(e, e.dataTransfer.files, instanceOptions);
        }

        function dragenter(e) {
            e.stopPropagation();
            e.preventDefault();
            if (dragClass) {
                addClass(dropbox, dragClass);
            }
        }

        function dragleave(e) {
            if (dragClass) {
                removeClass(dropbox, dragClass);
            }
        }

        function dragover(e) {
            e.stopPropagation();
            e.preventDefault();
            if (dragClass) {
                addClass(dropbox, dragClass);
            }
        }
    }

    // setupCustomFileProperties: modify the file object with extra properties
    function setupCustomFileProperties(files, groupID) {
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            file.extra = {
                nameNoExtension: file.name.substring(0, file.name.lastIndexOf('.')),
                extension: file.name.substring(file.name.lastIndexOf('.') + 1),
                fileID: i,
                uniqueID: getUniqueID(),
                groupID: groupID,
                prettySize: prettySize(file.size)
            };
        }
    }

    // getReadAsMethod: return method name for 'readAs*' - http://www.w3.org/TR/FileAPI/#reading-a-file
    function getReadAsMethod(type, readAsMap, readAsDefault) {
        for (var r in readAsMap) {
            if (type.match(new RegExp(r))) {
                return 'readAs' + readAsMap[r];
            }
        }
        return 'readAs' + readAsDefault;
    }

    // processFileList: read the files with FileReader, send off custom events.
    function processFileList(e, files, opts) {

        var filesLeft = files.length;
        var group = {
            groupID: getGroupID(),
            files: files,
            started: new Date()
        };

        function groupEnd() {
            group.ended = new Date();
            opts.on.groupend(group);
        }

        function groupFileDone() {
            if (--filesLeft === 0) {
                groupEnd();
            }
        }

        FileReaderJS.output.push(group);
        setupCustomFileProperties(files, group.groupID);

        opts.on.groupstart(group);

        // No files in group - end immediately
        if (!files.length) {
            groupEnd();
            return;
        }

        var supportsSync = sync && FileReaderSyncSupport;
        var syncWorker;

        // Only initialize the synchronous worker if the option is enabled - to prevent the overhead
        if (supportsSync) {
            syncWorker = makeWorker(workerScript);
            syncWorker.onmessage = function(e) {
                var file = e.data.file;
                var result = e.data.result;

                // Workers seem to lose the custom property on the file object.
                if (!file.extra) {
                    file.extra = e.data.extra;
                }

                file.extra.ended = new Date();

                // Call error or load event depending on success of the read from the worker.
                opts.on[result === "error" ? "error" : "load"]({ target: { result: result } }, file);
                groupFileDone();
            };
        }

        Array.prototype.forEach.call(files, function(file) {

            if(typeof file.extra === "undefined") file.extra = {};

            file.extra.started = new Date();

            if (opts.accept && !file.type.match(new RegExp(opts.accept))) {
                opts.on.skip(file);
                groupFileDone();
                return;
            }

            if (opts.on.beforestart(file) === false) {
                opts.on.skip(file);
                groupFileDone();
                return;
            }

            var readAs = getReadAsMethod(file.type, opts.readAsMap, opts.readAsDefault);

            if (syncWorker) {
                syncWorker.postMessage({
                    file: file,
                    extra: file.extra,
                    readAs: readAs
                });
            }
            else {

                var reader = new FileReader();
                reader.originalEvent = e;

                fileReaderEvents.forEach(function(eventName) {
                    reader['on' + eventName] = function(e) {
                        if (eventName == 'load' || eventName == 'error') {
                            file.extra.ended = new Date();
                        }
                        opts.on[eventName](e, file);
                        if (eventName == 'loadend') {
                            groupFileDone();
                        }
                    };
                });
                reader[readAs](file);
            }
        });
    }

    // checkFileReaderSyncSupport: Create a temporary worker and see if FileReaderSync exists
    function checkFileReaderSyncSupport() {
        var worker = makeWorker(syncDetectionScript);
        if (worker) {
            worker.onmessage =function(e) {
                FileReaderSyncSupport = e.data;
            };
            worker.postMessage({});
        }
    }

    // noop: do nothing
    function noop() {

    }

    // extend: used to make deep copies of options object
    function extend(destination, source) {
        for (var property in source) {
            if (source[property] && source[property].constructor &&
                source[property].constructor === Object) {
                destination[property] = destination[property] || {};
                arguments.callee(destination[property], source[property]);
            }
            else {
                destination[property] = source[property];
            }
        }
        return destination;
    }

    // hasClass: does an element have the css class?
    function hasClass(el, name) {
        return new RegExp("(?:^|\\s+)" + name + "(?:\\s+|$)").test(el.className);
    }

    // addClass: add the css class for the element.
    function addClass(el, name) {
        if (!hasClass(el, name)) {
          el.className = el.className ? [el.className, name].join(' ') : name;
        }
    }

    // removeClass: remove the css class from the element.
    function removeClass(el, name) {
        if (hasClass(el, name)) {
          var c = el.className;
          el.className = c.replace(new RegExp("(?:^|\\s+)" + name + "(?:\\s+|$)", "g"), " ").replace(/^\s\s*/, '').replace(/\s\s*$/, '');
        }
    }

    // prettySize: convert bytes to a more readable string.
    function prettySize(bytes) {
        var s = ['bytes', 'kb', 'MB', 'GB', 'TB', 'PB'];
        var e = Math.floor(Math.log(bytes)/Math.log(1024));
        return (bytes/Math.pow(1024, Math.floor(e))).toFixed(2)+" "+s[e];
    }

    // getGroupID: generate a unique int ID for groups.
    var getGroupID = (function(id) {
        return function() {
            return id++;
        };
    })(0);

    // getUniqueID: generate a unique int ID for files
    var getUniqueID = (function(id) {
        return function() {
            return id++;
        };
    })(0);

    // The interface is supported, bind the FileReaderJS callbacks
    FileReaderJS.enabled = true;

})(this, document);