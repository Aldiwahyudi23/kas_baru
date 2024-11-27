 @extends('user.layout.app')

 @section('content')
 <div class=" col-12">
     <!-- select2bs4 EXAMPLE -->
     <div class="card card-default ">
         <div class="card-header">
             <h3 class="card-title">Edit Data Kas</h3>

             <div class="card-tools">
                 <button type="button" class="btn btn-tool" data-card-widget="collapse">
                     <i class="fas fa-plus"></i>
                 </button>
                 <button type="button" class="btn btn-tool" data-card-widget="remove">
                     <i class="fas fa-times"></i>
                 </button>
             </div>
         </div>
         <!-- /.card-header -->
         <div class="card-body">
             <form action="{{ route('kas.update',Crypt::encrypt($kas_payment->id)) }}" method="POST"
                 enctype="multipart/form-data" id="adminForm">
                 @method('PATCH')
                 {{csrf_field()}}
                 <!-- Jumlah Pembayaran -->
                 <div class="form-group">
                     <label for="amount">Jumlah Pembayaran <span class="text-danger">*</span></label>
                     <input type="text" name="amount_display" id="amount_display"
                         value="{{ old('amount',$kas_payment->amount) ? number_format(old('amount',$kas_payment->amount), 2, ',', '.') : '' }}"
                         class="form-control col-12 @error('amount') is-invalid @enderror"
                         placeholder="Masukkan nominal yang diajukan" oninput="formatIndonesian(this)">
                     <input type="hidden" name="amount" id="amount" value="{{ old('amount',$kas_payment->amount) }}">
                 </div>

                 <!-- Metode Pembayaran -->
                 <div class="form-group">
                     <label for="payment_method">Metode Pembayaran</label>
                     <span class="text-danger">*</span></label>
                     <select class="select2bs4 @error('payment_method') is-invalid @enderror" style="width: 100%;"
                         name="payment_method" id="payment_method" onchange="toggleTransferReceipt()">
                         <option value="">--Pilih Pembayaran--</option>
                         <option value="cash"
                             {{ old('payment_method',$kas_payment->payment_method) == 'cash' ? 'selected' : '' }}>Cash
                         </option>
                         <option value="transfer"
                             {{ old('payment_method',$kas_payment->payment_method) == 'transfer' ? 'selected' : '' }}>
                             Transfer</option>
                     </select>
                 </div>

                 <!-- Upload Bukti Transfer (jika metode transfer) -->
                 <div class="form-group" id="transfer_receipt"
                     style="display: {{ old('payment_method',$kas_payment->payment_method) == 'transfer' ? 'block' : 'none' }};">
                     <label for="transfer_receipt_path">Upload Bukti Transfer</label>
                     <span class="text-danger">*</span></label>
                     <input type="file" name="transfer_receipt_path" id="transfer_receipt_path" accept="image/*"
                         class="form-control col-12 @error('transfer_receipt_path') is-invalid @enderror">
                     @error('transfer_receipt_path')
                     <div class="invalid-feedback">{{ $message }}</div>
                     @enderror
                 </div>

                 <!-- Thumbnail Tanda Bukti Transfer -->
                 <div class="form-group col-6 col-sm-2">
                     <a href="{{ asset($kas_payment->transfer_receipt_path) }}" data-toggle="lightbox"
                         data-title="Tanda Bukti Transfer - {{$kas_payment->code}}" data-gallery="gallery">
                         <img src="{{ asset($kas_payment->transfer_receipt_path) }}" class="img-fluid mb-2"
                             alt="white sample" />
                     </a>
                 </div>

                 <div class="form-group">
                     <label for="description" class="col-sm-12 col-form-label">Keterangan
                         <span class="text-danger">*</span></label>
                     <textarea class="form-control col-12 @error('description') is-invalid @enderror" name="description"
                         id="description">{{ old('description',$kas_payment->description) }}</textarea>
                 </div>
                 <input type="hidden" name="data_warga_id" value="{{$kas_payment->data_warga_id}}">
                 <!-- Button Submit -->
                 <button type="submit" class="btn btn-success" id="submitBtns">Update Kas</button>
             </form>
             <br>
             <!-- /.card-body -->
             <div class="card-footer">
                 Catatan: <p>- Masukan data sesuai kebutuhan dan benar.
                     <br>- Bertanda bintang Merah wajib di isi.
                 </p>
             </div>

         </div>
     </div>
     <!-- /.card -->
 </div>
 @endsection

 @section('script')

 <script>
     // Function to toggle the visibility of the transfer receipt input based on selected payment method
     function toggleTransferReceipt() {
         var paymentMethod = document.getElementById('payment_method').value;
         var transferReceipt = document.getElementById('transfer_receipt');
         transferReceipt.style.display = (paymentMethod === 'transfer') ? 'block' : 'none';
     }
 </script>
 @endsection