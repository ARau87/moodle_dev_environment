/**
 * setup-jsme-render.js
 * 
 * Script handling the setup of the jsme editor.
 * 
 * @copyright  2018 Andreas Rau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

console.log('TEST');

function initializeChemdrawJsmeEditor(){

        // We need to wait for jQuery to load.
        if ($) {

            if(window.location.href.indexOf('review.php') !== -1){
    
                var editorFormFields = $('.qtype_chemdraw_answer_section');
    
                for (let i = 0; i < editorFormFields.length; i++) {
    
                    new ChemDrawJSMEEditor(i,
                        $(editorFormFields[i]).find('.qtype_chemdraw_jsme_container'),
                        {
                            molInputSelector: null,
                            smilesInputSelector: $(editorFormFields[i]).find('.qtype_chemdraw-jsme-input'),
                            readonly: true,
                            default: { type: 'smiles', value: $(editorFormFields[i]).find('.qtype_chemdraw-jsme-input').val() }
    
                        }
                    );
    
    
    
                }
    
            }
            else {
    
                var editorFormFields = $('.qtype_chemdraw_answer_section');

                console.log(editorFormFields);
    
                for (let i = 0; i < editorFormFields.length; i++) {
    
                    new ChemDrawJSMEEditor(i,
                        $(editorFormFields[i]).find('.qtype_chemdraw_jsme_container'),
                        {
                            molInputSelector: null,
                            smilesInputSelector: $(editorFormFields[i]).find('.qtype_chemdraw-jsme-input'),
                            readonly: false,
                            default: { type: 'smiles', value: $(editorFormFields[i]).find('.qtype_chemdraw-jsme-input').val() }
    
                        }
                    );
    
    
    
                }
    
            }
    
        }
        else {
            setTimeout(initializeChemdrawJsmeEditor, 50);
        }

}

const qtypeChemdrawInit = setInterval(function() {

    if(JSApplet.JSME){

        initializeChemdrawJsmeEditor();
        clearInterval(qtypeChemdrawInit);


    }

}, 1000);