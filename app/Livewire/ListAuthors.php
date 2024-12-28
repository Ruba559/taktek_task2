<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Author;
use App\Models\Platform;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class ListAuthors extends Component
{


    use WithFileUploads;

    public $authors , $author;

    public  $state =[];
    
    public $search_text ,$type_order , $order;
    protected $listeners = ['closeModal' => 'closeModal'];
    public $orderDirection = 'asc';

   
  
    public $image2  ;


    public $rows = []; 

  

    public function addRow()
    {

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


        $validatedData = Validator::make($this->state, [
            'name' => 'required|string|max:255',
            'image' => 'nullable',
            'work' => 'required',
            'summary' => 'nullable'
        ])->validate();

      
        $this->validate([
            'rows.*.name' => 'required',
            'rows.*.url' => 'required',
            'rows.*.image' => 'nullable',
        ]);
    
        DB::beginTransaction();

        try {

   
        if(!$this->author)
        {
            $author = Author::create($validatedData);
            $author->update([ 'image' => $this->storeImage()]);
      
        if($this->rows)
        {
         
            foreach($this->rows as $item)
            {
            
                $author->platforms()->createMany([
                    [
                        'name' =>  $item['name'],
                        'url' =>  $item['url'],
                        'image' =>  $item['image'],
                    ]
                ]);
                
            }
        }
   
    }else{
        $this->author->update(
            $validatedData
        );

        if ($this->image) {
            if ($this->author->image) {
               
                 
                 unlink(public_path() . '/storage/' . $this->author->image);
               
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
                ]);
    
                
            }
        }
   
    }

        DB::commit();
    } catch (\Exception $e) {
        DB::rollback();
        
    }
    
    $this->dispatch('close-modal');
    }

    

    public function edit($id)
    {

        $this->author = Author::where('id' , $id)->first();
    
      //  $this->fill(['state.name' => $this->author->name , 'state.work' => $this->author->work , 'state.summary' => $this->author->summary]);
        //$this->fill(['state' =>  $this->author->toArray()]);
    

      $this->state = $this->author->toArray();

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
      
        $this->rows = [];
        $this->state = [];

        $this->author = null;
      
        $this->dispatch('close-modal');
        
    }
    public function storeImage()
    {
       if (isset($this->state['image'])) {
        $name = $this->state['image']->store('authors', 'public');

        return $name;
           
       }
       return Null;
    }

   
public function deleteImage()
{
  
    if (isset($this->state['image'])) {
        $this->state['image'] ='';
    }
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
