<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Contact;

class ContactsController extends Controller
{	
	//membuat properti limit 
	private $limit = 5;

	private $rules = [
			'name'    => ['required', 'min:5'],
			'company' => ['required'],
			'email'   => ['required', 'email'],
			'photo'   => ['mimes:jpg,jpeg,png,gif,bmp']
    	];

    private $upload_dir = 'public/uploads';

    public function __construct()
    {
    	$this->upload_dir = base_path() . '/' . $this->upload_dir;
    }

    public function autocomplete(Request $request)
    {
    	if ($request->ajax())
    	{
			return Contact::select(['id', 'name as value'])->where(function($query) use ($request) {
	    						if ( ($term = $request->get("term")) ) 
	    						{
	    							$keywords = '%' . $term . '%';
	    							$query->orWhere("name", 'LIKE', $keywords);
	    							$query->orWhere("company", 'LIKE', $keywords);
	    							$query->orWhere("email", 'LIKE', $keywords);
	    						}
	    	})
						    	->orderBy('name', 'asc')
						    	->take(5)
						    	->get();
    	}
    	
    }

    public function index(Request $request)
    {
    	
    	$contacts = Contact::where(function($query) use ($request) {
    						if ($group_id = ($request->get('group_id'))) {
    							$query->where('group_id', $group_id);
    						}

    						if ( ($term = $request->get("term")) ) 
    						{
    							$keywords = '%' . $term . '%';
    							$query->orWhere("name", 'LIKE', $keywords);
    							$query->orWhere("company", 'LIKE', $keywords);
    							$query->orWhere("email", 'LIKE', $keywords);
    						}
    	})
					    	->orderBy('id', 'desc')
					    	->paginate($this->limit);

    	//mengembalikan view ke dalam folder contacts/index.php dengan nilai data compact contact(database)
    	return view('contacts.index', compact('contacts'));
    }

    public function create()
    {
    	//mengembalikan view kedalam folder kontak/create.blade.php
    	return view('contacts.create');
    }

    public function store(Request $request)
    {
    	//melakukan request dan validasi input
		$this->validate($request, $this->rules);
		
		$data = $this->getRequest($request);
		
    	Contact::create($data);

    	return redirect('contacts')->with('message', 'Contact Tersimpan!');
    }

    public function getRequest(Request $request)
    {
	    $data = $request->all();

    	if ($request->hasFile('photo'))
    	{
	    	//membuat variable foto dengan menampung filename
			$photo       = $request->file('photo');
			$fileName    = $photo->getClientOriginalName();
			//membuat variable dengan menampung lokasi upload file 
			$destination = $this->upload_dir;
	    	//memindahkan foto ke dalam folder temporari
	    	$photo->move($destination, $fileName);

	    	//memasukan namafile kedalam data
	    	$data['photo'] = $fileName;
		}

    	return $data;
    }

    public function update($id, Request $request)
    {
    	
    	$this->validate($request, $this->rules);

		$contact  = Contact::find($id);
		$data     = $this->getRequest($request);
		//mengambili foto lama
		$oldPhoto = $contact->photo;
    	$contact->update($data);
    	//jika foto lama tidak sama dengan foto baru maka akan di hapus
    	if ($oldPhoto !== $contact->photo) {
    		$this->removePhoto($oldPhoto);
    	}

    	return redirect('contacts')->with('message', 'Contact Update!');
    }

    public function edit($id)
    {
    	//mengambil data kotak berdasarkan id yang di tampung dalam $contact
    	$contact = Contact::find($id);
    	//melewatkan data $contact melalui view contact/edit.blade.php
    	return view("contacts.edit", compact('contact'));
    }

    public function destroy($id)
    {
    	$contact = Contact::find($id);

    	$contact->delete();

    	$this->removePhoto($contact->photo);

    	return redirect('contacts')->with('message', 'contact berhasil di hapus!');
    }

    public function removePhoto($photo)
    {
    	//memastikan foto tidak kosong
    	if ( ! empty($photo))
    	{
    		//ketika foto ada kita buat alamat lengkapnya foto tsb
    		$file_path = $this->upload_dir . '/' . $photo;
    		//jika filenya benar benar ada maka akan di hapus foto tsb
    		if (file_exists($file_path) ) unlink($file_path);
    	}
    }
}
