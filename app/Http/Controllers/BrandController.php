<?php

namespace App\Http\Controllers;

use App\Http\Constants\PaginateConstant;
use App\Http\Requests\Brand\BrandStoreRequest;
use App\Http\Requests\Brand\BrandUpdateRequest;
use App\Repositories\Brand\BrandRepositoryInterface;
use App\Traits\CheckCountSlug;
use App\Traits\SessionTrait;
use Illuminate\Support\Str;
class BrandController extends Controller
{

    use SessionTrait, CheckCountSlug;

    protected BrandRepositoryInterface $brandRepo;

    public function __construct(BrandRepositoryInterface $brandRepo)
    {
        $this->brandRepo = $brandRepo;
    }
    /**
     *
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $brand = $this->brandRepo->paginateWithOrder(PaginateConstant::PERPAGE, 'id', 'desc');

        return view('backend.brand.index')->with('brands', $brand);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.brand.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BrandStoreRequest $request)
    {
        $data = $request->all();
        $slug = Str::slug($request->title);
        $count = $this->brandRepo->getCountBySlug($slug);
        $slug = $this->countSlug($count, $slug);
        $data['slug'] = $slug;

        $status = $this->brandRepo->create($data);
        if ($status){
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->route('brand.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $brand = $this->brandRepo->find($id);

        if(!$brand){
            $this->error();
        }
        return view('backend.brand.edit')->with('brand',$brand);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BrandUpdateRequest $request, $id)
    {
        $data=$request->all();

        $status = $this->brandRepo->update($id, $data);
        if($status){
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->route('brand.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $status = $this->brandRepo->delete($id);

        if($status){
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->route('brand.index');
    }
}
