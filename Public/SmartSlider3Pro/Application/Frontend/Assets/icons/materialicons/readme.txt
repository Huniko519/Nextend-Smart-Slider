https://fonts.googleapis.com/icon?family=Material+Icons

class: .material-icons



var a = <---- https://material.io/icons/data/grid.json;
var b = a.icons;
var result = {};
for(var k in b){
    result[b[k].ligature] = b[k].ligature;
    result[b[k].ligature] = {"name": b[k].ligature, "kw": b[k].keywords.join(',')};
}
console.log(JSON.stringify(result));

Usage: <i class="material-icons">alarm</i>