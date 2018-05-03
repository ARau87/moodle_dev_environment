/**
 * setup-jsme.js
 * 
 * Script handling the setup of the jsme editor.
 * 
 * @copyright  2018 Andreas Rau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * This function is called automatically by JSME after the jsme 
 * script is loaded in the browser.
 */
function jsmeOnLoad(){
    jsmeApplet = new JSApplet.JSME('jsme_container', '380px', '340px');

    drawSmilesTextInput('#id_answer', jsmeApplet);

    addStructureChangeListener(jsmeApplet);
    addInputFieldListener('#id_answer', jsmeApplet);
}

/**
 * Check if the smiles text input is filled at the start (when editing a question). If so
 * draw the smiles string in the editor.
 * 
 * @param selector CSS selector of the input field
 * @param jsmeApplet Reference to the jsme editor
 */
function drawSmilesTextInput(selector, jsmeApplet){

    if($(selector).val() !== ''){
        jsmeApplet.readGenericMolecularInput($(selector).val());
    }

}

/**
 * Adding an event listener to the jsme editor that fires if the
 * structure drawn is changing. 
 * 
 * @param jsmeApplet A reference to the jsmeApplet object
 */
function addStructureChangeListener(jsmeApplet){

    const smilesInputField = $('#id_answer');

    jsmeApplet.setCallBack('AfterStructureModified', structureModifiedCallback.bind(null, smilesInputField, jsmeApplet));

}

/**
 * Callback that is invoked when the structure drawn in the editor changes.
 * When the structure changes the smiles string of the current structure should
 * be written into the smiles input field.
 * 
 * @param smilesInputField A reference to the smiles input field.
 * @param jsmeApplet A reference to the jsmeApplet
 */
function structureModifiedCallback(smilesInputField, jsmeApplet){

    smilesInputField.val(jsmeApplet.smiles());
}

/**
 * If the smiles text input field is changing we read the value of the field and
 * draw the structure
 * 
 * @param selector The CSS selector of the input field
 * @param jsmeApplet A reference to the jsme editor
 * 
 */
function addInputFieldListener(selector,jsmeApplet){

    $(selector).on('keyup',function(event){

        jsmeApplet.readGenericMolecularInput(event.target.value);

    });


}
