https://linearicons.com/free#cheat-sheet

Class: .lnr

.lnr-mustache

https://linearicons.com/free
$('.mtl a span').remove()
var result = {},
glyph = $('.mtl a');

for(var i = 0; i < glyph.length; i++){
var k = glyph.eq(i).html().trim().replace(/lnr\-/, '');
result[k] = {"name": k, "kw": glyph.eq(i).data('liga')};
}
console.log(JSON.stringify(result));