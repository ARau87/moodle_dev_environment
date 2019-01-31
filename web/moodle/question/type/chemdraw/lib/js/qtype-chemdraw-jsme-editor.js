/**
 * jsme-editor.js
 * 
 * Provides an wrapper of the JSME editor. This is needed to add custom functionalities like readonly or 
 * binding the editor to moodle form elements.
 * 
 * @copyright  2018 Andreas Rau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * ChemDrawJSMEEditor constructor
 * 
 * Creates an Editor object that holds the reference to the selectors where 
 * the editor is created and where the import text fields are located. 
 * It also handles that the correct editor is bound to the related import fields and vice versa.
 * 
 * @param {Number} id The id of editor
 * @param {string} parentSelector The selector where the editor should be created
 * @param {object} options An options object that holds some required information
 *                         Example: {
 *                                      molInputSelector: 'selector_of_mol_inputfield',
 *                                      smilesInputSelector: 'selector_of_mol_inputfield',
 *                                      default: {type: 'mol|smiles', value: 'string to be loaded on page load'}
 *                                  }
 */
function ChemDrawJSMEEditor(id, parentSelector, options) {

    // Create a container in the parent element to be sure that the element id is correct!
    this.editorId = 'qtype_chemdraw_jsme_editor_' + id;
    this.container = $('<div class="qtype_chemdraw_editor_container" id="' + this.editorId + '"></div>');

    parentSelector.prepend(this.container);

    this.jsme = new JSApplet.JSME(this.editorId, '100%', '300px');


    if (!options.readonly) {

        this.initBindings(options);
        this.handleDefaultValues(options);
        this.addEditorListener();

        if(options.isInBackground !== undefined){
            this.isInBackground = options.isInBackground;
            if(this.isInBackground){
                this.sendToBackground();
            }
        }
    }

    else if (options.readonly) {
        this.jsme.readGenericMolecularInput(options.default.value);

        if(options.isInBackground !== undefined){
            this.isInBackground = options.isInBackground;
        }

        this.handleReadonly();
    }

}

/**
 * Sends the editor to the background
 */
ChemDrawJSMEEditor.prototype.sendToBackground = function(){

    $('#' + this.editorId).parent().addClass('qtype_chemdraw_background');

}

/**
 * Brings the editor to the front
 */
ChemDrawJSMEEditor.prototype.bringToFront = function(){

    $('#' + this.editorId).parent().removeClass('qtype_chemdraw_background');

}

/**
 * Handles the default values in the input fields when the page is loading by updating the editor
 * 
 * @param {object} options An option object containing the selectors of the input fields and the default values
 */
ChemDrawJSMEEditor.prototype.handleDefaultValues = function (options) {

    // Handling default string option
    if (options.default) {
        options.default.type === 'smiles' ? this.smiles = options.default.value : this.smiles = '';
        options.default.type === 'mol' ? this.mol = options.default.value : this.mol = '';

        //Initial update of the editor depending on the default string
        options.default.type === 'smiles' ? this.update('smiles') : undefined;
        options.default.type === 'mol' ? this.update('mol') : undefined;
    }
    else {
        this.smiles = '';
        this.mol = '';
    }

}

/**
 * As there is no way to set the editor readonly we need to simulate a readonly functionality. This is fulfilled by
 * waiting for the editor to load the default structure and afterwards the hole editor is replaced by a clone of itself.
 * The consequence of this is that all listeners are removed from the editor and therefore it is readonly.
 * 
 */
ChemDrawJSMEEditor.prototype.handleReadonly = function () {

    this.jsme.setCallBack('AfterStructureModified', () => {

        $(this.container)[0].parentNode.replaceChild($(this.container)[0].cloneNode(true), $(this.container)[0]);

        if(this.isInBackground){
            this.sendToBackground();
        }
    });


}

/**
 * Handles the binding of the Editor to the related input fields
 * 
 * @param {object} options An options object that contains among others the selectors of the input fields
 */
ChemDrawJSMEEditor.prototype.initBindings = function (options) {

    if ('smilesInputSelector' in options) {
        this.bindToImportField(options.smilesInputSelector, 'smiles');
    }

    if ('molInputSelector' in options) {
        this.bindToImportField(options.molInputSelector, 'mol');
    }

    if ('fileInputSelector' in options) {
        this.bindToImportField(options.fileInputSelector, 'file');
    }

    if('dropzoneSelector' in options) {
        this.bindToImportField(options.dropzoneSelector, 'dropzone');
    }

}

/**
 * Binds the ChemDrawJSMEEditor to the corresponding import fields
 * 
 * @param {string} selector Selector of the input field the editor should bind to.
 * @param {'smiles'|'mol'|'file'} type The import type.
 */
ChemDrawJSMEEditor.prototype.bindToImportField = function (selector, type) {

    if (type === 'smiles') {

        this.smilesInput = $(selector);
        this.addSmilesInputFieldListener(this.smilesInput);
    }

    else if (type === 'mol') {

        this.molInput = $(selector);
        this.addMolInputFieldListener(this.molInput);
    }

    else if (type === 'file') {
        this.fileInput = $(selector);
        this.addFileInputListener(this.fileInput);
    }

    else if (type === 'dropzone'){

        this.dropzone = $(selector);
        this.setupFileDropzone(this.dropzone);

    }

    else {

        throw 'Type ' + type + ' is not supported!'

    }

}

/**
 * 
 * @param {*} selector The selector of the file dropzone
 */
ChemDrawJSMEEditor.prototype.setupFileDropzone = function(selector){

    const element = $(selector);



    element.on('dragover', (event) => {
        event.preventDefault();
        event.stopPropagation();
        element.addClass('on_drag_over');
        element.removeClass('on_success');
        element.removeClass('on_error');
    });

    element.on('dragleave', (event) => {
        event.preventDefault();
        event.stopPropagation();
        element.removeClass('on_drag_over');
    });

    element.on('drop', (event) => {
        event.preventDefault();
        event.stopPropagation();
        element.removeClass('on_drag_over');
        element.removeClass('on_success');
        element.removeClass('on_error');

        event.dataTransfer = event.originalEvent.dataTransfer;
        if (window.File && window.FileReader && window.FileList && window.Blob) {

            const file = event.dataTransfer.files[0];
            const fileExtension = file.name.split('.')[file.name.split('.').length-1];
            const reader = new FileReader();
            const dropzoneTextElement = element.find('.qtype_chemdraw_dropzone_text');

            if(fileExtension === 'mol' || fileExtension === 'smiles' || fileExtension === 'smi' || fileExtension === 'rxn'){
                reader.onload = (function (self) {
                    return function (e) {
                        self.readFile(e.target.result);
                    }
                })(this);
                reader.readAsText(file);
                element.addClass('on_success');
                dropzoneTextElement.text('File successfully loaded: ' + file.name);

            }
            else {
                element.addClass('on_error');
                dropzoneTextElement.text('File format not supported!')
            }

        }

        else {
            alert('Reading files is not supported!!!');
            throw 'Reading files is not supported!!!';
        }

        this.update('file');
    });

}

/**
 * Updates the editor and input fields
 * 
 * @param {'mol'|'smiles'|'editor'|'file'} updated Defines which of the variables changed to only update the other two
 */
ChemDrawJSMEEditor.prototype.update = function (updated) {

    if (updated === 'mol') {

        this.jsme.readGenericMolecularInput(this.mol);

        if (this.smilesInput) this.smilesInput.val(this.jsme.smiles());

    }

    else if (updated === 'smiles') {

        this.jsme.readGenericMolecularInput(this.smiles);

        if (this.molInput) {
            this.molInput.val(this.jsme.molFile(true));
        }

    }

    else if (updated === 'editor') {

        if (this.smilesInput) this.smilesInput.val(this.jsme.smiles());
        if (this.molInput) this.molInput.val(this.jsme.molFile(true));

    }

    else if (updated === 'file') {

        this.jsme.readGenericMolecularInput(this.file);
        $(this.molInput).trigger('change');
        $(this.smilesInput).trigger('change');

    }



}

/**
 * Reads the file data and invokes the update of the editor
 * 
 * @param {string} fileData The file content
 */
ChemDrawJSMEEditor.prototype.readFile = function (fileData) {

    this.file = fileData;
    this.update('file');

}

/**
 * Adds a file event listener that fires when a file is uploaded and enters the files
 * content into the editor using the File API.
 * 
 * @param {object} selector The selector of the file import
 */
ChemDrawJSMEEditor.prototype.addFileInputListener = function (selector) {
    $(selector).on('change', (event) => {

        if (window.File && window.FileReader && window.FileList && window.Blob) {

            const file = event.target.files[0];
            const reader = new FileReader();

            reader.onload = (function (self) {
                return function (e) {
                    self.readFile(e.target.result);
                }
            })(this);
            reader.readAsText(file);

        }

        else {
            throw 'Reading files is not supported!!!';
        }

        this.update('file');
    })
}

/**
 * Adds 'keyup' listeners to the smiles input fields that update the jsme editor
 * 
 * @param {object} selector The selector of an input field a listener should be added to.
 */
ChemDrawJSMEEditor.prototype.addSmilesInputFieldListener = function (selector) {

    $(selector).on('keyup', (event) => {
        this.smiles = event.target.value;
        this.update('smiles');
    });

}

/**
 * Adds 'keyup' listeners to the mol input fields that update the jsme editor
 * 
 * @param {object} selector The selector of an input field a listener should be added to.
 */
ChemDrawJSMEEditor.prototype.addMolInputFieldListener = function (selector) {

    $(selector).on('keyup', (event) => {
        this.mol = event.target.value;

        this.update('mol');
    });

}

/**
 * Adds an listener to the editor that invokes a callback when the drawn structure changes.
 */
ChemDrawJSMEEditor.prototype.addEditorListener = function () {

    this.jsme.setCallBack('AfterStructureModified', () => {

        this.update('editor');

    });

}

/**
 * Binds the toggle button to the JSME instance
 * @param {object} button A reference to the DOMElement of the toggle Button
 */
ChemDrawJSMEEditor.prototype.bindToggleButton = function(button){

    button.on('click', this.onToggleButtonClick.bind(this));

}

/**
 * Handler called when the toggle button is clicked
 * @param {object} event
 */
ChemDrawJSMEEditor.prototype.onToggleButtonClick = function(event){

    event.preventDefault();

    this.isInBackground = !this.isInBackground;

    this.isInBackground ? this.sendToBackground() : this.bringToFront();

}
