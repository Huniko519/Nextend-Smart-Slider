http://www.typicons.com/

var result = {},
glyph = $('#preview > div');

for(var i = 0; i < glyph.length; i++){
var k = glyph.eq(i).data('name').trim();
result[k] = {"name": k, "kw": glyph.eq(i).data('match').trim()};
}
console.log(JSON.stringify(result));