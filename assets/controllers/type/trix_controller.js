import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        // Configuración antes de inicializar
        document.addEventListener('trix-before-initialize', () => {
            // 1. Cambiar toolbar (minimalista)
            Trix.config.toolbar.getDefaultHTML = () => `
                <div class="trix-button-row">
  <span class="trix-button-group trix-button-group--text-tools" data-trix-button-group="text-tools">
    <button type="button" class="trix-button trix-button--icon trix-button--icon-bold" data-trix-attribute="bold" data-trix-key="b" title="Negrita" tabindex="-1">Negrita</button>
    <button type="button" class="trix-button trix-button--icon trix-button--icon-italic" data-trix-attribute="italic" data-trix-key="i" title="Cursiva" tabindex="-1">Cursiva</button>
    <button type="button" class="trix-button trix-button--icon trix-button--icon-strike" data-trix-attribute="strike" title="Tachado" tabindex="-1">Tachado</button>
    <!--<button type="button" class="trix-button trix-button--icon trix-button--icon-link" data-trix-attribute="href" data-trix-action="link" data-trix-key="k" title="Insertar enlace" tabindex="-1">Insertar enlace</button>-->
  </span>

  <span class="trix-button-group trix-button-group--block-tools" data-trix-button-group="block-tools">
    <button type="button" class="trix-button trix-button--icon trix-button--icon-heading-1" data-trix-attribute="heading1" title="Encabezado" tabindex="-1">Encabezado</button>
    <!--<button type="button" class="trix-button trix-button--icon trix-button--icon-quote" data-trix-attribute="quote" title="Quote" tabindex="-1">Quote</button>-->
    <!--<button type="button" class="trix-button trix-button--icon trix-button--icon-code" data-trix-attribute="code" title="Code" tabindex="-1">Code</button>-->
    <button type="button" class="trix-button trix-button--icon trix-button--icon-bullet-list" data-trix-attribute="bullet" title="Lista sin numerar" tabindex="-1">Lista sin numerar</button>
    <button type="button" class="trix-button trix-button--icon trix-button--icon-number-list" data-trix-attribute="number" title="Lista numerada" tabindex="-1">Lista numerada</button>
    <button type="button" class="trix-button trix-button--icon trix-button--icon-decrease-nesting-level" data-trix-action="decreaseNestingLevel" title="Decrementar nivel" tabindex="-1">Decrementar nivel</button>
    <button type="button" class="trix-button trix-button--icon trix-button--icon-increase-nesting-level" data-trix-action="increaseNestingLevel" title="Incrementar nivel" tabindex="-1">Incrementar nivel</button>
  </span>

  <!--<span class="trix-button-group trix-button-group--file-tools" data-trix-button-group="file-tools">
    <button type="button" class="trix-button trix-button--icon trix-button--icon-attach" data-trix-action="attachFiles" title="Adjuntar archivos" tabindex="-1">Adjuntar archivos</button>
  </span>-->

  <span class="trix-button-group-spacer"></span>

  <span class="trix-button-group trix-button-group--history-tools" data-trix-button-group="history-tools">
    <button type="button" class="trix-button trix-button--icon trix-button--icon-undo" data-trix-action="undo" data-trix-key="z" title="Deshacer" tabindex="-1">Deshacer</button>
    <button type="button" class="trix-button trix-button--icon trix-button--icon-redo" data-trix-action="redo" data-trix-key="shift+z" title="Rehacer" tabindex="-1">Rehacer</button>
  </span>
</div>
            `;
        });

        document.addEventListener('trix-change', function (event) {
            document.querySelectorAll('trix-editor').forEach(editor => {
                editor.editor.element.focus();
                let textarea = document.getElementById(editor.getAttribute('input'));
                textarea.value = editor.value;
                console.log(textarea.value);
                textarea.dispatchEvent(new Event('change', {bubbles: true}));
            });
        });

        import('trix').then(({default: Trix}) => {
            import('trix/dist/trix.min.css').then(() => {

            });
        });
    }

}
