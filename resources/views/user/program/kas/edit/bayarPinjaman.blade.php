 @extends('user.layout.app')

 @section('content')
 <div class=" col-12">
     <!-- select2bs4 EXAMPLE -->
     <div class="card card-default ">
         <div class="card-header">
             <h3 class="card-title">Edit Data Pembayaran Pinjaman</h3>

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
             <form action="{{ route('bayar-pinjaman.update',Crypt::encrypt($bayarPinjaman->id)) }}" method="POST"
                 enctype="multipart/form-data" id="adminForm">
                 @method('PATCH')
                 {{csrf_field()}}
                 <!-- Jumlah Pembayaran -->

                 <div class="form-group">
                     <label for="amount">Jumlah Pembayaran <span class="text-danger">*</span></label>
                     <input type="text" name="amount_display" id="amount_display"
                         value="{{ old('amount',$bayarPinjaman->amount) ? number_format(old('amount',$bayarPinjaman->amount), 0, ',', '.') : '' }}"
                         class="form-control col-12 @error('amount') is-invalid @enderror"
                         placeholder="Masukkan nominal yang diajukan" oninput="formatIndonesian(this)">
                     <input type="hidden" name="amount" id="amount" value="{{ old('amount',$bayarPinjaman->amount) }}">
                 </div>

                 <!-- Metode Pembayaran -->
                 <div class="form-group">
                     <label for="payment_method">Metode Pembayaran</label>
                     <span class="text-danger">*</span></label>
                     <select class="select2bs4 @error('payment_method') is-invalid @enderror" style="width: 100%;"
                         name="payment_method" id="payment_method" onchange="toggleTransferReceipt()">
                         <option value="">--Pilih Pembayaran--</option>
                         <option value="cash"
                             {{ old('payment_method',$bayarPinjaman->payment_method) == 'cash' ? 'selected' : '' }}>Cash
                         </option>
                         <option value="transfer"
                             {{ old('payment_method',$bayarPinjaman->payment_method) == 'transfer' ? 'selected' : '' }}>
                             Transfer</option>
                     </select>
                 </div>

                 <!-- Upload Bukti Transfer (jika metode transfer) -->
                 <div class="form-group" id="transfer_receipt"
                     style="display: {{ old('payment_method',$bayarPinjaman->payment_method) == 'transfer' ? 'block' : 'none' }};">
                     <label for="transfer_receipt_path">Upload Bukti Transfer</label>
                     <input type="file" name="transfer_receipt_path" id="transfer_receipt_path" accept="image/*"
                         value="{{old('payment_method',$bayarPinjaman->payment_method)}}"
                         class="form-control col-12 @error('transfer_receipt_path') is-invalid @enderror"
                         onchange="preview('.tampil-gambar', this.files[0])">
                     <div class="tampil-gambar mt-3">
                         @if (isset($bayarPinjaman->transfer_receipt_path))
                         <a href="{{ asset( $bayarPinjaman->transfer_receipt_path) }}" data-toggle="lightbox"
                             data-title="Tanda Bukti Transfer - {{$bayarPinjaman->code}}" data-gallery="gallery">
                             <img src="{{ asset($bayarPinjaman->transfer_receipt_path) }}" class="img-fluid mb-2"
                                 alt="white sample" width="100px" />
                         </a>
                         @endif
                     </div>
                 </div>

                 <div class="form-group">
                     <label for="description" class="col-sm-12 col-form-label">Keterangan
                         <span class="text-danger">*</span></label>
                     <textarea class="form-control col-12 @error('description') is-invalid @enderror" name="description"
                         id="description">{{ old('description',$bayarPinjaman->description) }}</textarea>
                 </div>
                 <input type="hidden" name="data_warga_id" value="{{$bayarPinjaman->data_warga_id}}">
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