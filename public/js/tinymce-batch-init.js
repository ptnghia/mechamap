/**
 * tinymce-batch-init.js
 * Batch + lazy initialization for TinyMCE editors used in MechaMap.
 * - Auto-detect elements with class .tinymce-auto
 * - Lazy initialize via IntersectionObserver (viewport + user interaction)
 * - Provides programmatic API: window.TinyMCEBatch.initSingle(el)
 */
(function(window, document){
    'use strict';

    if (window.TinyMCEBatch) return; // prevent double load

    const BATCH = {
        initialized: new Set(),
        observer: null,
        waiting: [],
        tinymceReady: false
    };

    function log(){ /* console.debug('[TinyMCEBatch]', ...arguments); */ }

    function whenTinyMCEReady(cb){
        if (window.tinymce) { BATCH.tinymceReady = true; return cb(); }
        BATCH.waiting.push(cb);
        if (!document.__tinymcePolling){
            document.__tinymcePolling = true;
            const iv = setInterval(()=>{
                if (window.tinymce){
                    clearInterval(iv);
                    BATCH.tinymceReady = true;
                    BATCH.waiting.splice(0).forEach(fn=>{ try{ fn(); }catch(e){} });
                }
            },40);
            setTimeout(()=>clearInterval(iv), 10000);
        }
    }

    function pickConfig(context, selector, options){
        const cfgFactory = new TinyMCEConfig();
        switch(context){
            case 'admin': return cfgFactory.getAdminConfig(selector, options);
            case 'showcase': return cfgFactory.getShowcaseConfig(selector, options);
            case 'minimal': return cfgFactory.getMinimalConfig(selector, options);
            default: return cfgFactory.getCommentConfig(selector, options);
        }
    }

    function enhanceConfig(baseCfg, meta){
        baseCfg.setup = function(editor){
            TinyMCEConfig.addCustomButtons(editor);
            TinyMCEConfig.addEventHandlers(editor);

            editor.on('init', function(){
                const loading = document.getElementById(meta.id + '-loading');
                if (loading) loading.style.display = 'none';
                const textarea = document.getElementById(meta.id);
                if (textarea) textarea.style.display = 'none';
                TinyMCEUploader.initDragDrop(meta.id);
                TinyMCEUploader.initPasteHandler(meta.id);
            });

            editor.on('input keyup change', function(){
                const textarea = document.getElementById(meta.id);
                if (!textarea) return;
                const content = editor.getContent().trim();
                textarea.value = content;
                textarea.dispatchEvent(new Event('input', { bubbles: true }));
                if (content && meta.required){
                    textarea.classList.remove('is-invalid');
                    const err = document.getElementById(meta.id + '-error');
                    if (err) err.style.display = 'none';
                    const container = editor.getContainer();
                    if (container) container.classList.remove('is-invalid');
                }
            });
        };

        baseCfg.images_upload_handler = function(blobInfo, success, failure, progress){
            return TinyMCEUploader.uploadImage(blobInfo, success, failure, progress);
        };

        baseCfg.file_picker_callback = function(callback, value, meta){
            TinyMCEUploader.filePicker(callback, value, meta);
        };

        return baseCfg;
    }

    function initElement(el){
        if (!el || BATCH.initialized.has(el)) return;
        const id = el.getAttribute('id') || el.dataset.editorId;
        if (!id){ log('missing id'); return; }

        // show loading
        const loading = document.getElementById(id + '-loading');
        if (loading) loading.style.display = 'block';

        const context = el.dataset.context || 'comment';
        const height = parseInt(el.dataset.height || '200',10);
        const placeholder = el.dataset.placeholder || '';
        const required = el.dataset.required === '1';
        const selector = '#' + id.replace(/(:|\.|\[|\]|,)/g, '\\$1');

        whenTinyMCEReady(()=>{
            try {
                const baseCfg = pickConfig(context, selector, { height, placeholder, required });
                const finalCfg = enhanceConfig(baseCfg, { id, required });
                window.tinymce.init(finalCfg).then(()=>{
                    BATCH.initialized.add(el);
                }).catch(err=>{
                    console.error('TinyMCE init failed', err);
                    if (loading) loading.style.display = 'none';
                });
            } catch (e){
                console.error('TinyMCE config error', e);
                if (loading) loading.style.display = 'none';
            }
        });
    }

    function scan(){
        const nodes = document.querySelectorAll('textarea.tinymce-auto');
        nodes.forEach(el => {
            if (!el.dataset.lazy) {
                // immediate or observed depending on viewport
                if (isInViewport(el, 0.1)) initElement(el); else observe(el);
            } else {
                observe(el);
            }
        });
    }

    function isInViewport(el, threshold){
        const rect = el.getBoundingClientRect();
        const vh = window.innerHeight || document.documentElement.clientHeight;
        const visible = rect.top < vh * (1 + threshold) && rect.bottom > 0;
        return visible;
    }

    function observe(el){
        if (!('IntersectionObserver' in window)) { initElement(el); return; }
        if (!BATCH.observer){
            BATCH.observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting){
                        BATCH.observer.unobserve(entry.target);
                        initElement(entry.target);
                    }
                });
            }, { rootMargin: '200px 0px', threshold: 0.05 });
        }
        BATCH.observer.observe(el);
    }

    // Public API
    window.TinyMCEBatch = {
        initAll: scan,
        initSingle: initElement,
        isInitialized: el => BATCH.initialized.has(el)
    };

    document.addEventListener('DOMContentLoaded', scan);

})(window, document);
