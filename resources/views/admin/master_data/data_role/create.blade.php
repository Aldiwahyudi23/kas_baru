            <div class="card-body">
                <form action="{{ route('role.store') }}" method="POST" enctype="multipart/form-data" id="adminForm">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-sm-6">

                            <div class="form-group">
                                <label for="name">Nama <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" value="{{old('name')}}" class="form-control col-12 @error('name') is-invalid @enderror">
                            </div>

                            <div class="form-group">
                                <label for="is_active">Status Aktif</label>
                                <select class="form-control col-12 @error('is_active') is-invalid @enderror" name="is_active">
                                    <option value="">--Pilih Status--</option>
                                    <option value="1" {{ old('is_active') == '1' ? 'selected' : ''}}>Aktif</option>
                                    <option value="0" {{ old('is_active') == '0' ? 'selected' : ''}}>Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">

                            <div class="form-group">
                                <label for="description" class="col-sm-2 col-form-label">Deskripsi <span class="text-danger">*</span></label>
                                <div class="col-sm-12">
                                    <textarea class="summernote-textarea form-control col-12 @error('description') is-invalid @enderror" name="description" id="description">{{ old('description') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Button Submit -->
                    <button type="submit" class="btn btn-success" id="submitBtns">Create</button>
                </form>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                Catatan: <p>- Masukan data sesuai kebutuhan dan benar.
                    <br>- Bertanda bintang Merah wajib di isi.
                </p>
            </div>