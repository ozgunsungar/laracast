// Youtube başlangıç

***********TEMEL PHP DERSLERI*****************

echo 'hello <br>' . PHP_EOL;
--> PHP.EOL ile backendde alta atar htmlde ise <br> ile

// strlen(text)

strtoupper()--> türkçe karakterler büyümez
mb_strtoupper()--> türkçe karakterler de büyür.

// Anonim fonksiyon

$anonym = function() use ($sayi1){ --> disarda tanimlananlari kullanma
    echo "hello";
};
$anonym();

// single line if
isnull($sayi3) ? sayi2 : sayi3;

//arrayleri printlemek
print_r($renkler);

// foreach ile key value dönmek
foreach($value as $itemKey => $itemValue){
    echo "$itemKey: $itemValue <br>";

}

//stringi substringlere ayırmak - key=>value şeklinde array oluşturur.
$string = "apple,orange,banana,grape";
$delimiter = ",";

// Split the string into an array using the specified delimiter
$fruitsArray = explode($delimiter, $string);
out..
Array
(
[0] => apple
[1] => orange
[2] => banana
[3] => grape
)

//array_filter() foknsiyonu
$ikiyeBolunen = array_filter($sayilar, function($item){
    return $item%2==0
});

//json veri dönüsümü
json_encode($oersons); gettype-->string
json_decode($jsonObj); gettype-->array

//Superglobals

<from action="" method="POST"> -->action boş olduğu için bulunduğu sayfaya gider.
    <input type="text" name="firstName" placeholder="Adınız">
    <input type="submit" value="Gönder">
</from>
        --> formun içindeki resimleri doğru iletmek için form taginin içine enctype="multipart/form-data" yazman gerekiyor.
        --> bu form post ile gönderildiğinden yazılan değeri çekmek için
if($_SERVER["REQUEST_METHOD"]=="POST"){
    $firstName = $_REQUEST['firstName'];
    echo $firstName;
} --> şeklinde değeri getirebiliriz.

//include/require işlemleri
require ile cagrildiginda dosya yoksa hata verir include vermez.

//sessions ve cookie
$_SESSION['name']=$name;
setcookie("name",$name,time()+3600);

unset($_SESSION['name']);
setcookie("name","", time()-3600);


-->özel parantezden kurtulmak için \( şeklinde yazabiliriz.

***********PHP OOP*****************
//php 7.4'ten itibaren attribute tipi tanımlaması yapılmamaktadır.
// veri tipi belirtme
public function getExperience(): int{}
private string $name;

// sabit attr tanımlamak

const SALARY = 9000;
self::SALARY şeklinde kullan

//abstract class Teacher
//abstract function getLastname();
//final ile tanımlanan classı birkez daha türetemezsin.

//Bir classa bir kez extends işlemi yapılırken birden fazla implements yapılabilir.

//trait kullanarak birden fazla inheritance kullanıyor gibi yapabiliriz
trait Message{
    public function setFullName(){}
}
class Matematik Extends Teacher{
    use Message;
}
//Interfaceler contracts isimli klasörde,
  traitler traits klasöründe
  index public klasöründe
  veri tabanındaki herbir tabloya karşılık gelen  models klasörü.

// namespaceleri klasörün ismi olarak ver
use models\Matematik;
use public\Matematik as newMatematik; -->şeklnde tanimlama yaparsak isim cakismasi da olmaz.

//is_callable
function write(){
echo "yazdim";
}
$text = "write"
var_dump(is_callable($text));


//REGEX
$text = "Selamlar arkadaşlar,ben Sercan Özen";
^-$ başlayan-biten
$pattern = "#(.*?)lar#" -->.*her şey olur, ? ise olabilir de olmayabilir de olasılığını getirir.
preg_match($pattern,$text,$matchesAll);-->matchesAll'a hepsini dönecek.


//Call User Func
#className = "Employee";
call_user_func([new $className, 'work'],"Tuğçe","Sercan"); --> en son kısımlar args. yani func parametreleri.
call_user_func_array([new $className,"work"],["Tuğçe","Sercan"]);
---> Statik çağırmak için ise
call_user_func($className . "::run")--> run burada bir statik method


***********PHP OOP*****************
//basic .htaccess
RewriteEngine On
RewriteRule ^([0-9a-zA-Z]+)$ index.php[QSA]

***********Route Project*****************

//core klasörü içine Route.class oluştur.
//rotalarımızı web.php'nin içinde çağıracağız

//laravelde route çağırma kısımları kısımları web.php'ye yazılır.
Route::get|post("/",function(){}); şeklinde, bu tarz functionlar Closure olarak geçer.


//helpers içine function yazılır. ex:
if (!function_exists("dd")){
    function dd(mixed $data): void{
    print_r($data);
    die();
}
}

//route tanımlama app\Core içindeki Route.class içinde yapılır.
private static array $routes = [];
public static function get(): Route{};-->içerisine url ve action alır.
public static function post(string $url,$action): Route{};
ya da "" "" post(string $url, \Closure|string $action){};

function içinde return new self(); yaparak zincirleme çağırmayı tetikliyorsun.
static olduğu için self yazıyoruz. olmasaydı this anahtar kelimesiyle yapacaktık.

//$_SERVER["REQUEST_URI"] , $_SERVER["REQUEST_METHOD"]

//route ile ilgili ana işlemler bittikten sonra dispatch diye bir function kullanacağız web.php içinde
dispatch Route.class içinde tanımlanacak

//json ile mesaj yollamak
echo json_encode([
    "message"=> "Method desteklenmiyor."
]);
exit();

**// url web.php de tanımladığımız get ve post ile çağırdığımız pathler.
     uri ise web sayfasının arama kısmında (link) yazan yer.


**//dirname($_SERVER['DOCUMENT_ROOT']); yaparsan proje dizininin pathini almış oluruz.
Bu sayede kolayca url'leri ayarlayabiliriz.

//404.php dosyasını include_once yaparak uri değiştirmeden gösterebiliriz.
// html'ler resources altında gözükür.

//extract kullanımı
extract() içine gelen arrayleri ayırır ve valuelara keyler yardımıyla ulaşmamızı sağlar.
return ve require işlemlerinin hemen üstünde kullan ki yollayabil.

//newleyip çağırma
call_user_func_array([new ("app\Controllers\\".$controllerClass),$controllerMethod],$parameters);
**hangi method çalışacaksa newledikten sonra onu çağırıyorsun.


//where kullanımı
Route::get("/users/{id}/works/{work_id}",function(){})->where(['id'=>"([0-9]+)",'work_id'=>"([0-9a-z]+)"});

//name kullanımı

//prefix
Route::prefix("/admin")->group(function(){
    Route::get("/","AdminController@index");
    Route::get("/register","AdminController@index");
    /***
    */admin/
    */admin/register 
    */
});

//css ve js dosyalarına erişim
private static function assetInclude(array $explodeExtension,int $extensionIndex,strşng $filePath): void{
$contentType = $explodeExtension[$extensionIndex] == "css" ? "text/css" : "application/javascript";
heade("Content-type: $contentType");
include $filePath;
exit();
}
--> bu methodu kullanıp checkRoute içinde dosya kontrolü yap, explode ve count işlemleriyle uzantının css olup olmadığını bul.

//<input class="form-control"> yazarsan ortalanıp gelir.

//jquery cdn dünya genelinde ortak ağ üzerinde olan bi sistem.Web sitelerin dosyalarını hızlı yüklemek ve indirmek için kullanılır.
Ön belleğe alınıp başka sitelerde de kolay çalışabilir.

//jqueryde sayfa hazr olunca çalışacak kısım.
<script>
    $(document).ready(function (){
       let fullname = $("#fullname");

       btnNext.click(function (){
           if(fullname.val()=="" || fullname.val()==null){

           }
       })
    });
</script>

//inputValidate
let inputValidate = [
    'fullname'=>"required|min:3|max:30",
    "email"=>"required|min:3|max:30|type:email"
]