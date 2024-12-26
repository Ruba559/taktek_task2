<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Author;
use App\Models\Platform;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;

class ListAuthors extends Component
{


    use WithFileUploads;

    public $authors , $author;
    
    public $search_text ,$type_order , $order;
    protected $listeners = ['closeModal' => 'closeModal'];
    public $orderDirection = 'asc';

    public $name , $image , $work , $summary ;
  
    public $image2  ;


    public $rows = []; 

    protected $rules = [
        'name' => 'required',
        'rows.*.name' => 'required',
        'rows.*.url' => 'required',
        'rows.*.image' => 'required',
      ];
    
  
    public function updated($propertyName)
    {
      $this->validateOnly($propertyName);
    }

    public function addRow()
    {
        dd('ji');

        $this->rows[] = [
            'name' => '',
            'url' => '',
            'image' => '',
        ];
    }
    public function deleteRow($index)
    {
       
        unset($this->rows[$index]);
       
        $this->rows = array_values($this->rows);
    }
    public function save()
    {
dd('j');
        $this->validate(); 
       DB::beginTransaction();

       try {
        if(!$this->author)
        {
          
        $author = Author::create([
            'name' => $this->name,
            'image' => $this->storeImage(),
            'work' => $this->work,
            'summary' => $this->summary,
        ]);
       
        if($this->rows)
        {
            // $this->validate([
            //     'rows.*.name' => 'required',
            //     'rows.*.url' => 'required',
            //     'rows.*.image' => 'nullable',
            // ]);
    
            foreach($this->rows as $item)
            {
    
                Platform::create([
                    'name' =>  $item['name'],
                    'url' =>  $item['url'],
                    'author_id' => $author->id,
                    'image' =>  $item['image'],
                   // 'image' => $this->storeImagePlatform($item['image']),
                ]);
    
                
            }
        }
   
    }else{
        $this->author->update([
            'name' => $this->name,
            'work' => $this->work,
            'summary' => $this->summary,
           
        ]);

        if ($this->image) {
            if ($this->author->image) {
               
                    if (File_exists(public_path() . '/storage/' . $this->author->image)) {
                        unlink(public_path() . '/storage/' . $this->author->image);
                   
                }

                $this->author->update(['image' => $this->storeImage()]);
            } else {

                $this->author->update(['image' => $this->storeImage()]);
            }
        }

        
        $platforms = Platform::where('author_id', $this->author->id)->get();

        foreach ($platforms as $platform) {
            $platform->delete();
        }
        if($this->rows)
        {
            $this->validate([
                'rows.*.name' => 'required',
                'rows.*.url' => 'required',
            ]);
    
            foreach($this->rows as $item)
            {
    
                Platform::create([
                    'name' =>  $item['name'],
                    'url' =>  $item['url'],
                    'author_id' => $this->author->id,
                    'image' =>  $item['image'],
                   // 'image' => $this->storeImagePlatform($item['image'] ),
                ]);
    
                
            }
        }
   
    }
    
    DB::commit();
} catch (\Exception $e) {
    DB::rollback();
}

        $this->dispatchBrowserEvent('close-modal');
    }

    

    public function edit($id)
    {

        $this->author = Author::where('id' , $id)->first();
      
        $this->fill(['name' => $this->author->name , 'work' => $this->author->work , 'summary' => $this->author->summary]);

        $this->image2 =  $this->author->image;   

        $platforms = Platform::where('author_id', $this->author->id)->get();

        foreach ($platforms as $platform) {
       
            $item['name'] = $platform->name;
            $item['url'] = $platform->url;
            $item['image'] = $platform->image;
  
            array_push($this->rows, $item);
  
  
          }

         

    }
    public function closeModal()
    {
       
        $this->dispatchBrowserEvent('close-modal');

        $this->rows = [];
        $this->name = '';
        $this->work = '';
        $this->summary = '';
        $this->image = '';

        $this->author = null;

    }
    public function storeImage()
    {
       if (!$this->image) {

           return Null;
       }
           $name = $this->image->store('authors', 'public');

           return $name;
    }

   
public function deleteImage()
{
  
    $this->image = '';
    $this->image2 = '';
  
}




    public function destroy($id)
    { 
        
        Author::find($id)->delete();
 

        $platforms = Platform::where('author_id', $id)->get();

        foreach ($platforms as $platform) {
            $platform->delete();
        }
 
   }

  
   public function toggleSortDirection($type)
   {       $this->type_order = $type;
       $this->orderDirection = $this->orderDirection === 'asc' ? 'desc' : 'asc';
   }
   public function orderBy($type , $order)
   {
      $this->type_order = $type;
      $this->order = $order;
   
   }


    public function render()
    {

        
        if( $this->search_text != '')
        {
            $this->authors = Author::where('name', 'like', '%' . $this->search_text . '%')->get();
   
        }else{
            if($this->type_order == 'name')
            {
                $this->authors = Author::orderBy('name' ,  $this->orderDirection)->get();
            }if($this->type_order == 'work')
            {
                $this->authors = Author::orderBy('work' ,  $this->orderDirection)->get();
            }else{
                $this->authors = Author::orderBy('name' ,  $this->orderDirection)->get();
            }
            
        }
       


        return view('livewire.list-authors');
    }
}
