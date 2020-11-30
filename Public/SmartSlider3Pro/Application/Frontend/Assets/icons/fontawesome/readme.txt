https://nodeca.github.io/js-yaml/
https://raw.githubusercontent.com/FortAwesome/Font-Awesome/master/src/icons.yml

var a =  <--- json;
var b = a.icons;
var result = {};
for(var k in b){
result[b[k].id] = {"name": b[k].name, "kw": [b[k].name].concat(b[k].filter).filter(function(n){ return n != undefined }).join(',')};
}
console.log(JSON.stringify(result));