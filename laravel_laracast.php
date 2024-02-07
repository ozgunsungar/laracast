## laravel helper functions :
app_path()
base_path()
resource_path()

                        *******************The Basics Notes : *******************

Route::get('post/{post}', function ($slug) {

return view('post',['post'=>\App\Models\Post::find($slug)]);

})
->where('post','[A-z_\-]+');

//şeklinde route oluşturabiliyoruz. Post diye model oluşturduk bunun içinde generic olarak route dosyası
arayabiliyoruz.

public static function find($slug)
{
if (!file_exists($path = resource_path("post/{$slug}.html"))) {
throw new ModelNotFoundException();
}
return cache()->remember("posts.{$slug}", 1200, fn() => file_get_contents($path));
}
//Post classı içinde böyle bir method var. Gelen $slug'ın dosyası var mı bakıyor sonrasında
cacheleyip contentini çekiyor böylece sistem yorulmuyor.
terminalden cache()->forget('posts.all');

//Anasayfadaki bütün contentleri foreach ile yazdırmak için route içinde
return view('welcome',[
'posts'=>\App\Models\Post::all()
]);
//yapabiliriz sonrasında Post::all()içinde ise :
$files= File::files(resource_path("post/"));#tüm dosyaların pathini alır
return array_map(function($file){#array_map ile pathleri döneriz ve getContents ile içeriklerini alırız.
return $file->getContents();
},$files);
************
##YAML from matter sayesinde metadata kullanarak bütün bilgilere ayrı ayrı erişebiliyoruz.
dolayısıyla anasayfada içerikleri bastıracağımız kısımda (sonrasında all() function içinde değişiklik yapacağız)

$files= File::files(resource_path("post/")); ile klasördeki bütün dosyaların pathini array olarak alıyoruz.
$document = YamlFrontMatter::parseFile($file); ysonrasında for each içinde metadatayı parselıyoruz
ve oluşturduğumuz Post classından objeler üreterek $posts[] listesine atıyoruz

return view('welcome',[
'posts'=>$posts
]); ile de return işlemi gerçekleşiyor.

## Ancak bunun bir de collection kullanılarak yapılan daha temiz gözüken versiyonu var onun için ise :
#### array_map ile yapımı ####

Route::get('/', function () {
$files= File::files(resource_path("post/"));
$posts = array_map(function ($file){
$document = \Spatie\YamlFrontMatter\YamlFrontMatter::parseFile($file);

return new Post(
$document->title,
$document->excerpt,
$document->date,
$document->body(),
$document->slug
);
},$files);


return view('welcome',[
'posts'=>$posts
]);

#### collect ile ####

Route::get('/', function () {

return view('welcome',[
'posts'=> Post::all()
]);
});
Route::get('post/{post}', function ($slug) {

return view('post',['post'=>\App\Models\Post::find($slug)]);

})
->where('post','[A-z_\-]+');
-----Post class-----
public static function all()
{
return cache()->rememberForever('posts.all',function (){
return collect(File::files(resource_path("post/")))
->map(function ($file){
return \Spatie\YamlFrontMatter\YamlFrontMatter::parseFile($file);
})
->map(function ($document){
return new Post(
$document->title,
$document->excerpt,
$document->date,
$document->body(),
$document->slug
);
})
->sortByDesc('date');

});

}
public static function find($slug)
{
$posts = static::all();
return $posts->firstWhere('slug',$slug);
}
}
                                ******************* BLADE : *******************
##blade içinde yazabilirsin.
<a href="/post/{{$post->slug}}"></a>
@foreach()
{!!$post->body!!} ###--->>> html tagi kullanmak istiyorsan böyle yazman gerekiyor. Bizim kontrolümüzdeki dataya bunu uygulayabiliriz
ancak form gibi şeylerde kullanma user içine arbitrary script yazabilir.
@endforeach

####blade layout oluşturmanın iki farklı yolu var :
layout dosyası oluştur
1)layout dosyasında @yield('content') şeklinde belirt.
asıl blade dosyanda @extends("layout") ve @section('content') @endsection şeklinde yaz
2)Component oluşturmak
views içinde component klasörü oluştur ve layoutu buraya at
layout dosyasında {{$content}} yaz
asıl blade dosyanda
<x-layout>
    <x-slot name="content">
        icerik// seklinde yazilabilir.
    </x-slot>
</x-layout>
AMA x-slot şeklinde tanımlamak istemiyorsan layout kısmında {{$slot}} şeklinde tanımla
<x-layout>
    //icerik
</x-layout>


                            ******************* DATABASE : *******************

Ne işe yaradıklarına bakmak istiyosan
php artisan yaz gelen kodları oku.

//her tablonun ex: users bir single eloquent classı olur ex: user
user oluşturma örneği :
route ve tinker yardımıyla olabilir.
tinker için :
php artisan tinker
App\Models\User::all();
App\Models\User::count();


##tabloya tinker ile data yükleme (bunun diğer yolu php artisan make:migration add_column_articles_table --table=articles şeklinde oluyor içine yazıyorsun)
$user = new App\Models\User or $user = new User --> ikincisini tinker anlayabiliyor.
$user->name = 'test';
$user->email ='test@gmail.com';
$user->password = bcrypt('!password'); ---> bcrypt sayesinde parolaları güvenle tutabildik.
$user->save();
$user ,,, $user->name;
User::find(1); ---> idyle user bulma.
$user->update(['excerpt'=>'Changed'])

diğer oluşturma şekli Post::create(['title'=>'My Fourth Post','excerpt'=>'excerpt of this']); bunu yaparsan model içinde
fillable vs tanımlaman gerekir

//##Eloquent model :
php artisan make:model Post

//# Route Model Binding

Route::get('posts/{post:slug}', function (Post $post) {

return view('post',[
'post'=>$post
]);

});
uri wildcard name function içindekiyle aynı olması lazım.

ya da model kısmında getroutekeyname methodunu override et ve return olarak hangi columna göre arama yapacağını belirt.
return 'slug'; ya da return 'id'; gibi. id zaten default değeri.


##Eloquent Relationships
//hasOne, hasMany, belongsTo, belongsToMany

class Post extends Model içinde
public function category(){
    return $this->belongsTo(Category::class)
}

çağırırken $post->category şeklinde çağıracaksın. $post->category() (method şeklinde) olursa instance çağırır farklı bir şey olur
ilerde göreceksin.

blade içinde
<a href="#">{{$post->category->name}}</a> şeklinde çağırıp göstertebilirsin.

////# N+1 Problem için
Route::get('/',function(){
    return view('posts',[
        'posts'=>Post::with('category')->get()
])
})
Post::with('user')->first(); full posts along with the user who wrote it

----> normalde post tüm postlarda bulunan category kısımları
Select * From 'categories' where 'categories'.'id' = '1' limit 1 her post için tekrar çağıracak bunu id değerleri değişiyor.
ama with ile yazdığında
Select * From 'categories' where 'categories'.id in (1,2,3) oluyor.

php artisan migrate:fresh --seed


