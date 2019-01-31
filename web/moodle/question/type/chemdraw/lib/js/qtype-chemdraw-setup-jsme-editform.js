/**
 * setup-jsme-editform.js
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

    // We need to wait for jQuery to load.
    if($){
        var editorFormFields = $('[data-groupname^="qtype_chemdraw_jsme_editor"]');
        var importFormFields = $('[data-groupname^="qtype_chemdraw_file_import"]');

        for(let i = 0; i < editorFormFields.length; i++){

            new ChemDrawJSMEEditor(i, 
                       $(editorFormFields[i]).find('.col-md-9'),
                       {
                           molInputSelector: $(importFormFields[i]).find('.qtype_chemdraw_mol_import'),
                           fileInputSelector: $(importFormFields[i]).find('.qtype_chemdraw_file_picker'),
                           smilesInputSelector: $('#id_answer_' + i),
                           dropzoneSelector: $(importFormFields[i]).find('.qtype_chemdraw_dropzone'),
                           readonly: false,
                           default: { 
                               type: 'smiles', 
                               value: $('#id_answer_' + i).val()
                           }

                       }
                    );

            

        }

    }
    else {
        setTimeout(jsmeOnLoad, 50);
    }
}