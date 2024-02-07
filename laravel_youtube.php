css içinde
article + article {
    margin-top: 3rm  --->ilk article hariç hepsi top margin
}
********MVC DESIGN***********
View<->Controller<->Model<->Database

View'dan gelen istekler Controller tarafından karşılanır ne yapacağımıza burada karar veririz.

Route::get('posts/{post}',function($slug){
    $path=__DIR__."/../resources/posts/{$slug}.html";
    if(!file_exists($path)){
        dd('file does not exist');
}
    $post=file_get_contents($path);
    return view('post',['post'=>$post])
})

Route::get('post/{post}', function ($slug) {
if(!file_exists($path = __DIR__."/../resources/post/{$slug}.html")){
return redirect("/");
}
$post =cache()->remember("posts.{$slug}",1200,fn()=>file_get_contents($path)); ##unique key ve saniye verdik
return view('post',[
'post'=> $post
]);
})
->where('post','[A-z_\-]+');



********Blade-View-Controller Oluşturma***********

laravelde echo yerine {{"test"}} kullanılır.
.blade dosyasının içi html dışında bir şey yapmaz.

terminale -> php artisan make:controller HomeController yazarsak app>Http>Controllers içinde HomeController.php dosyası oluşturur.

**Route::get('/anasayfa',[\App\Http\Controllers\HomeController::class,'index'])->name("anasayfa");
'/anasayfa' uri gelince arrayin ilk elemanındaki classı çağır ve 2. elemandaki çağrılan classın 'index' isimli functionunu çağır.
**Route::get("/anasayfa","HomeController@index"); şeklinde çalışması için service providerda ayar yapılması gerek.
app>Providers>RouteServiceProvider
bu classın içinde Route::middleware için ->namespace($this->namespace) yap tabi öncesinde protected $namespace tanımla
protected $namespace = "\\App\\Http\\Controllers";

//PHP Storm plugins
.env files support
Idelog
Laravel Query
PHP Annotations
Symfony Support

//View klasör yapısı
Yapıları klasörlendir bulması kolay olsun.
örn : admin,email,front,layouts gibi
**layout içi
@include("layouts.front.navbar")
@yield("icerik")

**index içi
@extends("layouts.front")
@section("css")

//asset() fonksiyonu ile css ve js dosyalarını çağırmak.
<link rel="stylesheet" href="{{asset("assets/sweetalert2/dist/sweetalert2.min.css")}}">
<script src="{{asset("assets/jquery/dist/jquery.min.js")}}"></script>

//Aktif sayfanın gösterimi-*****
href="{{route("home")}}" -->buradaki home Route::get kısmında verdiğimiz name deki isim.
active classını kullanmak için ise :::
{{Route::is("home") ? "active" : ""}}
ya da
{{Route::currentRouteName()=="home") ? "active" : ""}}

//Redirect işlemi ile route yönlendirme
redirect(route("contact"));
redirect()->route("contact");

//@csrf token
bu token sayesinde forma unique kod atanır ve başka kaynaklardan gelen ataklardan korunmuş olur.
ya da value içine gömülü olarak {{csrf_token()}} kullanabiliriz.

//form action
action içine {{route("about")}} yazarsak formdaki bilgileri o sayfayı açar ve bilgileri açılan sayfaya taşır.

//Request kullanarak formdan gelen bilgileri alma
Request $request
dd(\request()->get("email"));
dd($request->email);
dd($request->all());

//Parametreli route kullanımı
<form action="{{route("user",["id"=>5,"name"=>"test"])}}" method="POST">


Route::post('/user/{id}/{name?}', "ContactController@user")   **-->name? opsiyonel olduğunu belirtir. Opsiyonel ifadesi son olmak zorunda. Arada bırakırsan link bozulur
->name("user")
->where(["id"=>"[0-9]+","name=>[a-z]+"]);

//route match kullanımı -- Birden fazla methodu desteklememize yarıyor.
Route::match(["get","post"],"/support-form","SupportFormController@support")->name("support-form.support");

//Route Patch-Put - Delete&Directive Method kullanımı
directive : viewın içerisinde @ ile başlayan her şeye denir.
@method("PATCH")  : böyle yazabilmek için form method POST olmak zorunda
or
{{method_field("PATCH")}}

//Route resource (aşağıdakiler birer route)
php artisan make:controller ArticleController --resource
index() listeleme için get
create() formu göstereceğimiz yer get
store(Request $request) depolacağımız yer post
show(string $id) herhangi bir veriyi göstereceğimiz zaman get
edit(string $id) veri getirmeye yarar  get
update(Request $request,string $id) put patch post
destroy(string $id) sileceğimiz yer delete or post

Route::resource("article","ArticleController"); web.php de böyle tanımlıyoruz
/article uri olursa index /article/create olursa create formu çağrılır.


console'da php artisan route:list yaparsan bütün routeları liste olarak görebilirsin.

//Route Api Resource
Route::apiResource("/api/article","Api/ArticleController");

php artisan make:controller Api/ArticleController --api

//Route where kullanımı constraints eklemek için
->whereNumber
->whereAlpha
->whereAlphaNumeric
->whereIn("role",["admin","user"]);

//Route prefix & group

Route::prefix("admin")->group(function(){
    Route::get("/edit-article","ArticleController@edit")->name("admin.articleEdit");
    Route::get("/article/{id}/delete","ArticleController@delete")->name("admin.articleDestroy");
})
group her zaman içine funcion alır

//Route controller ile birden fazla route kullanma
Route::controller(\App\Http\Controllers\UserController::class)->group(function(){
    Route::get("/get-user","getUser");
    Route::get("/delete-user","deleteUser");
});
Bu sayede tek bir classın içindeki methodları farklı routelar için kullanabildik. Gruplamanın farklı yolu.


//önemli not
post/put/patch isteklerini a href ile yollayamayız(ahref sadece get isteği atar). Ya formla yollayacaksın ya jqueryle ya da backendden istek

php artisan route:cache
php artisan route:clear
php artisan cache:clear
php artisan view:clear
php artisan optimize
php artisan config:clear

//Controllerdan view'a veri gönderme
class HomeController extends Controller{
    public function index(){
        $age = 28;
        return view("front.index",['age'=>$age]);
        //ya da compact("age") ya da ->with('age',$age)
        //Bu sayede route ile çağırdığımız controller function içinde view'a veri göndermiş olduk.
}
}
.blade içinde gelen değer : {{$age?? @$person->aa}} yaparsak hatayı göstermez. @ sayesinde. Undefined hata için geçerli


@section("icerik")
    @if(isset($person) && isset($person->age))

    @else

    @endif
@endsection

//custom directive method için app>providers>AppServiceProvider.php'deki boot'a yaz.

Blade::directive("auresMethod",function($value = null){
    $methods = ["DELETE","PUT","PATCH"]
    if (!in_array($value,$methods)){
        return "";
    }
    return '<input type="hidden" name="_method" value="'.$value.'">';
})

//COMPONENT KULLANIMI 1
php artisan make:component InputText
.blade içinde component çağırırken <x- şeklinde başla ve componentin ismini yaz./>

<x-input-text type"'text'"/> şeklinde.
içine verdiğimiz parametreler örneğin type bir parametredir, oluşan classın constructerında belirtilmelidir.
public function__construct(public string $type){}

input-text.blade içinde $type kullanabiliriz ve bu dosyada {{$attributes}} yazarak component kullanılırken direkt içine attributeler
yazıldıysa onları teker teker yazmana gerek kalmadan hepsini sırasıyla kendisi çeker.

//COMPONENT KULLANIMI 2
oluşturulan component bir template gibi title content ve footer gibi özellikleri olsun.
biz diğer .blade dosyasında bu componenti çağırım içine slotlarla hangi kısmına ne ekleyeceğimizi söylüyoruz.
@isset($content)
    //
@endisset



//Veri tabanına giriş, env&config incelemesi
.env dosyası içinde db connectiona bakabilirsin


//Migration
Veri tabanlarının içinde tablo oluşturulmak istendiği zaman migration kullanılır.
Bir nevi versiyonlamış oluruz. Ne zaman hangi değişikliği yapmış olur görüyoruz.
.\db\migrations\ klasörü

php artisan migrate yazıldığında migrationdaki tabloların db'ye işlenmesini sağlar

php aritsan make:migration create_articles_table    ----> create_tabloismi_table
php artisan make:migration create_articles_table(dosyaismi) --create=sercan(tabloismi)
php artisan migrate:rollback son atılan batchi geri alır.
php artisan migrate:rollback --step=2 ----> oluşuturulan son 2 tablo için ,, --batch
php artisan migrate:reset
php artisan migrate:refresh , önce reset sonra migrate eder
php artisan migrate:fresh ---> migrate tablosundaki idleri en baştan başlatır. vebütün datalar kaybolur.çalışan projede fresh yapma

//Migration tablo oluşturma
timestamp_create_articles_table.php dosyasına girip up() fonksiyonuna(tablodaki sütun tanımlamaları için)
$table->string("description")->nullable();
$table->tinyInteger("status")->default(0); (aktif olup olmadığını belli eder)şeklinde şeyler yazabiliriz.
$table->charset="utf8";
$table->collation="utf8_general_ci";

//tabloya column ekleme
php artisan make:migration add_column_articles_table --table=articles
$table->unsignedBigInteger("category_id")->after("body"); (hangi sütundan sonra ekleneceği)
$table->foreign("category_id")  ---> şuanki tabloda bulunan fk
    ->references("id")          ---> fk'nin geldiği tablodaki column ismi
    ->on("categories")          ---> fknin geldiği tablo
    ->onDelete("cascade");      ---> fk tablosu silindiğinde ne olacağı (cascade sil demek)




php make:migration change_column_name_status_to_is_active_articles_table --table=articles
$table->renameColumn("status","is_active");
Schema::drop("articles");  ---->tablo silme komutu

//Model, Factory(fake data), Seeder kullanımı
model sadece hangi sütunların kullanılıp kullanılmayacağını söylemek için
ama modelde bunlar tanımlı olmadan factory ve seeder çalışmaz.

php artisan make:model Article
php artisan make:model Category -m -s -f ********** tablo oluştururken bunu kullan.

***** Model ******

--->tablonun ismiyle modelin ismi genelde aynı olur ama farklı da olabilir.
protected $table = "articles"
#otomatik dolacaklar olanlar guarded, bizim datayla dolduracaksak fillable
soft delete olunca "deleted_at" gelir
protected $fillable = ['title','body','is_active','category_id','slug_name']; ---> bu alanların kullanılacağını söyler
protected $guarded = ['title']; -->bunlar hariç hepsini kullan.
use HasFactory, SoftDeletes;

php artisan make:model Category --controller
php artisan make:factory ArticleFactory

***** Factory ******
factory içinde fake data üretmek için
$title=>fake()->title
return [
    'title'=> $title,
    'body'=>fake()->paragraph,
    'slug_name'=>Str::slug($title) ----> sluglar uri için kullanılır. link içi kullanılacak string oluşturur.
    'seo_keywords'=>Str::slug(fake(->adress,","))
];

seeder tarafından factoryde üretilen veriler dbye aktarılır.
php artisan make seeder CategorySeeder

seeder içinde :
run(): void{
    Category::factory(10)->create(); ---> ilk önce 10 tane category oluşturur.
}
##seeder çalıştırmak için
DbSeeder içinde (run fonksiyonunun içinde devamı)
$this->call([
    CategorySeeder::class ---> CategorySeeder'ını doldurman lazım aşağıda belirtiyor
    ArticleSeeder:class
]);
##Category seederın run fonksiyonu :
Category::factory(10)->create(); ---> Category modelini çağırıyoruz.
php artisan db:seed ---> ana seed classını db olarak kullandık diğerlerini de orada çağırdık

tek çağırmak için php artisan db:seed --class=ArticleSeeder
