<?php

namespace App\Http\Controllers;

use App\Http\Constants\PaginateConstant;
use App\Http\Requests\Shipping\ShippingStoreRequest;
use App\Http\Requests\Shipping\ShippingUpdateRequest;
use App\Repositories\Shipping\ShippingRepositoryInterface;
use App\Traits\SessionTrait;

class ShippingController extends Controller
{
    use SessionTrait;
    protected ShippingRepositoryInterface $shippingRepo;

    public function __construct(ShippingRepositoryInterface $shippingRepo)
    {
        $this->shippingRepo = $shippingRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shipping = $this->shippingRepo->paginateWithOrder(PaginateConstant::PERPAGE, 'id', 'DESC');

        return view('backend.shipping.index')->with('shippings',$shipping);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.shipping.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ShippingStoreRequest $request)
    {
        $data = $request->all();
        $status = $this->shippingRepo->create($data);

        if($status){
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->route('shipping.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $shipping = $this->shippingRepo->find($id);

        if(!$shipping){
            $this->error();
        }
        return view('backend.shipping.edit')->with('shipping', $shipping);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ShippingUpdateRequest $request, $id)
    {
        $data = $request->all();
        $status = $this->shippingRepo->update($id, $data);
        if($status){
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->route('shipping.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $status = $this->shippingRepo->delete($id);
        if($status){
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->route('shipping.index');
    }
}
