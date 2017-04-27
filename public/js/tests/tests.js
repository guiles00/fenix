test( "hello test", function() {
ok( 1 == "1", "Passed!" );
});
/*test( "a test", 1, function() {
//var aceptar_button = $( "#aceptar" );
//console.debug(aceptar_button);
//ok()
aceptar_button.on( "click", function() {
ok( true, "body was clicked!" );
});
$('<input id="aceptar" type="button"/>').appendTo('#qunit-fixture');
$('input#aceptar').click()
//aceptar_button.click();
$('input#aceptar').trigger( "click" ,function() {
ok( true, "body was clicked!" );
});
});*/

test("MyTest1", function() {
 changeText();
 equal(document.getElementById(
    "myElement").innerHTML,"Hello qUnit");
});

test("MyTest2", function() {
 equal(document.getElementById(
    "myElement").innerHTML,"Hello qUnit");
});