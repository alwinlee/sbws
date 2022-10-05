$(document).ready(function() {
    $('#fixheadertbl').fixedHeaderTable({ footer: true, cloneHeadToFoot: false, altClass: 'odd', autoShow: false });
    $('#fixheadertbl').fixedHeaderTable('show', 500);
});
