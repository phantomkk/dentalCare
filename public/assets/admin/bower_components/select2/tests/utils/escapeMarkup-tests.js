<<<<<<< HEAD
module('Utils - escapeMarkup');

var Utils = require('select2/utils');

test('text passes through', function (assert) {
  var text = 'testing this';
  var escaped = Utils.escapeMarkup(text);

  assert.equal(text, escaped);
});

test('html tags are escaped', function (assert) {
  var text = '<script>alert("bad");</script>';
  var escaped = Utils.escapeMarkup(text);

  assert.notEqual(text, escaped);
  assert.equal(escaped.indexOf('<script>'), -1);
});

test('quotes are killed as well', function (assert) {
  var text = 'testin\' these "quotes"';
  var escaped = Utils.escapeMarkup(text);

  assert.notEqual(text, escaped);
  assert.equal(escaped.indexOf('\''), -1);
  assert.equal(escaped.indexOf('"'), -1);
});

test('DocumentFragment options pass through', function (assert) {
  var frag = document.createDocumentFragment();
  frag.innerHTML = '<strong>test</strong>';

  var escaped = Utils.escapeMarkup(frag);

  assert.equal(frag, escaped);
});
=======
module('Utils - escapeMarkup');

var Utils = require('select2/utils');

test('text passes through', function (assert) {
  var text = 'testing this';
  var escaped = Utils.escapeMarkup(text);

  assert.equal(text, escaped);
});

test('html tags are escaped', function (assert) {
  var text = '<script>alert("bad");</script>';
  var escaped = Utils.escapeMarkup(text);

  assert.notEqual(text, escaped);
  assert.equal(escaped.indexOf('<script>'), -1);
});

test('quotes are killed as well', function (assert) {
  var text = 'testin\' these "quotes"';
  var escaped = Utils.escapeMarkup(text);

  assert.notEqual(text, escaped);
  assert.equal(escaped.indexOf('\''), -1);
  assert.equal(escaped.indexOf('"'), -1);
});

test('DocumentFragment options pass through', function (assert) {
  var frag = document.createDocumentFragment();
  frag.innerHTML = '<strong>test</strong>';

  var escaped = Utils.escapeMarkup(frag);

  assert.equal(frag, escaped);
});
>>>>>>> 6647e7f68513f34b86ec6c59d3a99f618da1b2de
