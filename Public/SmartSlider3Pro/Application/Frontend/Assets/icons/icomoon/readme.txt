https://raw.githubusercontent.com/Keyamoon/IcoMoon-Free/master/IcoMoon-Free.json

var b = a.selection;
var result = {};
for(var k in b){
result[b[k].name] = {"name": b[k].name, "kw": b[k].ligatures};
}
console.log(JSON.stringify(result));