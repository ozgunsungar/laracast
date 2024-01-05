********MVC DESIGN***********

View<->Controller<->Model<->Database

View'dan gelen istekler Controller tarafından karşılanır ne yapacağımıza burada karar veririz.


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

//Request kullanarak formdan gelen bilgileri alma
Request $request
dd(\request()->get("email"));
dd($request->email);
dd($request->all());