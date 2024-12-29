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

    public  $stateData =[];
    
    public $search_text ,$type_order , $order;
    protected $listeners = ['closeModal' => 'closeModal'];
    public $orderDirection = 'asc';

   
  
    public $image2 ;


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
     

        $validatedData = Validator::make($this->stateData, [
            'name' => 'required|string|max:255',
            'image' => 'nullable',
            'work' => 'required',
            'summary' => 'nullable'
        ])->validate();

      
        $validatedPlatform = Validator::make($this->rows, [
            '*.name' => 'required',
            '*.url' => 'required',
            '*.image' => 'nullable',
        ], [
            '*.name.required' => 'the name is required',
        ])->validate();

      
    
        DB::beginTransaction();

        try {

   
        if(!$this->author)
        {
            if(isset($validatedData['image']))
            {
                $validatedData['image'] = $validatedData['image']->store('authors', 'public');
            }
            $author = Author::create($validatedData);
          
      
        if($this->rows)
        { 
                $author->platforms()->createMany(
                    $this->rows
                );
        }
   
    }else{
      
        if(isset($validatedData['image']) && $validatedData['image'] != $this->author->image)
        {
            $validatedData['image'] = $validatedData['image']->store('authors', 'public');
        }

  
        $this->author->update(
            $validatedData
        );
        
      
       

        if(!empty($this->rows))
        {
           if($this->author->platforms->count() > 0 )
           {
             $this->author->platforms->each->delete();
           }
           

                    $this->author->platforms()->createMany(
                        $this->rows
                    );
            
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

        $this->author = Author::find( $id);
      

      $this->stateData = $this->author->toArray();
    
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
      $this->stateData = [];

        $this->author = null;
      
        $this->dispatch('close-modal');
        
    }
 
   
public function deleteImage()
{
  
    if (isset($this->stateData['image'])) {
        $this->stateData['image'] ='';
    }
    $this->image2 = '';
  
}




    public function destroy($id)
    { 
        
        $author = Author::find($id);
 

        if($author->platforms->count() > 0 )
        {
          $author->platforms->each->delete();
        }
        $author->delete();
 
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
