<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Payments;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    use AuthorizesRequests;
    public function index () {
        $this->authorizeForUser(auth('admins')->user(), 'payment-list');

        $payments = Payments::orderBy('id','ASC')->get();
        return view('backend.pages.payments.index', compact('payments'));
    }
    public function delete ($id) {
        $this->authorizeForUser(auth('admins')->user(), 'payment-delete');

        $model = Payments::findOrFail($id);
        $model->delete();

        return redirect()->route('payments.index')->with(['success' => 'Uğurlu!']);
    }
}
