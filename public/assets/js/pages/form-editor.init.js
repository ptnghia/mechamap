/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!************************************************!*\
  !*** ./resources/js/pages/form-editor.init.js ***!
  \************************************************/
/*
Template Name: Dason - Admin & Dashboard Template
Author: Themesdesign
Website: https://themesdesign.in/
Contact: themesdesign.in@gmail.com
File: Form editor Init Js File - Updated for TinyMCE
*/

// TinyMCE initialization for admin forms
// This replaces the old CKEditor implementation
if (typeof tinymce !== 'undefined' && document.querySelector('#tinymce-classic')) {
    tinymce.init({
        license_key: 'gpl',
        selector: '#tinymce-classic',
        height: 200,
        menubar: false,
        plugins: ['advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview'],
        toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link image'
    });
} else {
    console.warn('TinyMCE not loaded or #tinymce-classic element not found');
}
/******/ })()
;
