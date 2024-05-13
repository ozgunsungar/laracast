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

////# N+1 Problem için ,,,,, Post.class'a attribute tanımla.
protected $with=['category','author']; #bu sayede n+1 problemini çözmüş olduk. Her post çağrıldığında kontrol sağlanacak

Post::with('user')->first(); full posts along with the user who wrote it
foreing keyler hangi tablodaysa onu çağırırken with ile çağır içine foreign key class ismini yaz key yaz.

----> normalde post tüm postlarda bulunan category kısımları
Select * From 'categories' where 'categories'.'id' = '1' limit 1 her post için tekrar çağıracak bunu id değerleri değişiyor.
ama with ile yazdığında
Select * From 'categories' where 'categories'.id in (1,2,3) oluyor.

php artisan migrate:fresh --seed

///Factory oluşturmak
factory classında default değerler tanımlıyorsun ancak bunları override edip ezebiliyorsun
return [
'category_id'=>Category::factory()->create()->id, --->> return edilirken ayn ızamanda categoryden de factory çalışacak
'title'=>fake()->sentence,
]; şeklinde tanımlayabilirsin

defaul değeri ezme işlemi ise şu şekilde oluyor:

$user = User::factory()->create([
'name'=>'John Doe'
]);

####Yukarıda user istediğimiz isimle oluştu.Diğer sütunlar factoryden geldi. Aşağıda ise oluşan postların hepsi
yukarıdaki user_id kullansın istedik.

Post::factory(5)->create([
'user_id'=>$user->id
]);

/// En yeni postu en yukarda göstermek :

return view('posts',[
'posts'=> Post::latest()->with('category')->get()
]);
});

///#### postları author olarak çağıracağız ama tablodu author_id fksi olmadığı için aşağıda 'user_id' diye belirttik.

public function author(){ //user_id olarak arıyor.
return $this->belongsTo(User::class,'user_id');
}
}

                                ******************* Integrate the Design (CSS) : *******************
** images klasörünü public içine at
** componentlarda {{$slot}} isteğe bağlı belirtilir. view içinde <x-dosya-ismi/> seklinde cagrildiginde bu tagin
icine slotu yazabiliriz. acak slot yoksa direkt bu tagi cagirim componenti cekebiliriz.

** componentlar birden fazla kullanılmak için ancak bir kere kullanmak için bile ayırmak istediğin bir şey varsa partial
olarak ayırabilirsin.
viewin içine "_dosya-ismi.blade.php" seklinde yazabilirsin.Kullanacagin dosyada @include("partial_adi") seklinde kullanabilirsin.

<x-dosya-ismi/ :post"$post">

****### Component attributes: attribute ve props farklı
{{$slot}} içeriği belirliyordu. Ancak

@props(['type'])---> // Component dosyasının başındaki bu değer dosyada hangi attributeların kullanılacağını developera göstermek için. Kullanmasan da olur.
<div class="alert {{ $type }}">
    {{ $slot }}
</div> gibi bir kullanım da söz konusu. Burada $type componentın içinde tanımlanmış bir attribute. Componenti çağırırken

<x-alert type="success">
    Başarılı işlem!
</x-alert> şeklinde çağırmalıyız ki değeri içine atalım

*****Zamanı yazmak için
Published <time>{{$post->created_at->diffForHumans()}}</time>

style="grid-column; span 5"

### <x-post-card></x-post-card> içinde attribute tanımlarsan component blade'de aşağıdaki gibi kullanıyorsun.!!!!

<article {{$attributes->merge(['class'=>"transition-colors duration-300 hover:bg-gray-100 border border-black border-opacity-0 hover:border-opacity-5 rounded-xl"])}}
</article>

    @foreach($posts->skip(1) as $post)
    <x-post-card :post="$post"
                 class="{{$loop->iteration < 3 ? 'col-span-3' : 'col-span-2'}}"/>/>

    @endforeach
##col span kullanımı
<div class="lg:grid lg:grid-cols-6"> ----> dışarıdaki div'de bu şekilde grid sayısı belirliyorsun sonra altındaki divde
    ne kadar yer kaplayacağını söylüyorsun.
    ör :   <div class="col-span-2 ...">04</div></div> gibi


    #class içinde
hover : --> mouse üstüne gelince
focus : --> tab ya da yön tuşları ile üstüne gelince

htmlde attribute olarak style="display: none " yaparsan ne olursa olsun o kısmı göstermez
inline-flex

##### Category dropdown menu için js kullanıyoruz alpine.js libraryi githubdan alman gerekebilir. ya da chatgptye sor

###****Alpine.js eklentisi ile dropdown menu yapımı (laracast laravel 34. ders)

<div x-data="{show: false}" @click.away="show=false">
    <button
            @click="show = !show"
            class="py-2 pl-3 pr-9 text-sm font-semibold w-full lg:w-32 text-left flex lg:inline-flex"
    >
        {{isset($currentCategory) ? ucwords($currentCategory->name ): 'Categories'}}
        <svg class="transform -rotate-90 absolute pointer-events-none" style="right: 12px;" width="22"
             height="22" viewBox="0 0 22 22">
            <g fill="none" fill-rule="evenodd">
                <path stroke="#000" stroke-opacity=".012" stroke-width=".5" d="M21 1v20.16H.84V1z">
                </path>
                <path fill="#222"
                      d="M13.854 7.224l-3.847 3.856 3.847 3.856-1.184 1.184-5.04-5.04 5.04-5.04z"></path>
            </g>
        </svg>
    </button>
    <div x-show="show" class="py-2 absolute bg-gray-100 mt-2 rounded-xl w-full z-50" style="display:none">
        <a href="/"
           class="block text-left px-3 text-sm leading-6 hover:bg-blue-500 focus:bg-blue-500 hover:text-white focus:text-white">
            All
        </a>
        @foreach($categories as $category)
        <a href="/categories/{{$category->slug}}"
           class="block text-left px-3 text-sm leading-6
                           hover:bg-blue-500 focus:bg-blue-500 hover:text-white focus:text-white
                           {{isset($currentCategory) && $currentCategory->id === $category->id ? 'bg-blue-500 text-white' : ''}}
                           ">
            {{ucwords($category->name)}}
        </a>
        @endforeach
    </div>

</div>

-Flex kutuları, içerdikleri öğeleri düzenlemek ve hizalamak için kullanılan güçlü bir CSS düzen modelidir.
-Bir flex konteyneri, içerdiği öğeleri sıralamak ve hizalamak için esnek bir şekilde yapılandırılabilir.
-Flex konteyneri, display: flex; veya display: inline-flex; olarak stilendirilir.
-İlk seçenek, blok seviyesi bir yapı oluştururken, ikinci seçenek, satır içi bir yapı oluşturur.

## dropdown yükseklik ayarlama
class içine ---> overflow-auto max-h-4
div içine space-y-4 yazarsan childlarına margin atar

value="{{request('search') }}" ---> input içinde yap default value koyuyorsun (headerda css olarak yazıyorsun.)


                        ******************* SEARCH : *******************
**** Messy Way ****

Route::get('/', function () {
$posts = Post::latest();
if(request('search')){
$posts->where('title','like','%'.request('search').'%')
->orWhere('excerpt','like','%'.request('search').'%');
}

return view('posts', [
'posts' => $posts->get(),
'categories' => Category::all()
]);
})->name('home');

**** Cleaner Way (Query Scopes) ****
--->query scope methodunu eloquent modelin içinde tanımla.
----> request(['search']) yaparsan key value('search' => 'test') şeklinde array gelir. ama request('search') yaparsan sadece string döner
[request('search')] yaparsan indexi 0 valuesi string olan çıktıyı key value arrayi alırsın.


public function scopeFilter($query,array $filters)---->$query değeri aslında $posts. otomatik(Post::latest() sonucu) olarak geliyor
{                                                      $filters ise bizim filter() methoduna yolladığımız değer.

$query->when($filters['search'] ?? false,function ($query,$search){---->$search whenin içindeki sorgu kısmı. /false ise query değiştirilmeden aktarılır.
$query
->where('title', 'like', '%' . $search . '%')
->orWhere('excerpt', 'like', '%' . $search . '%');
});

}

****Önemli****
bir fonksiyon olsun $posts->filter() şeklinde. -> ile kullandığımız zaman ilk parametresi bir önceki kısımdan otomatik olarak gelir.
içine yazdığımız ikinci parametre olur.




******************* FILTERING : *******************


