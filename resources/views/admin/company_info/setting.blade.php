<form action="{{ route('tentang.update',Crypt::encrypt($companyInfo->id)) }}" class="form-horizontal" method="POST"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="form-group row">
        <label for="company_name" class="col-sm-2 col-form-label">Nama Perusahaan</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="company_name" placeholder="Name" name="company_name"
                value="{{ old('company_name', $companyInfo->company_name ?? '') }}" required>
        </div>
    </div>
    <div class="form-group row">
        <label for="description" class="col-sm-2 col-form-label">Deskripsi</label>
        <div class="col-sm-10">
            <textarea id="summernote" class="form-control summernote-textarea"
                name="description">{{ old('description', $companyInfo->description ?? '') }}</textarea>
        </div>
    </div>
    <div class="form-group row">
        <label for="vision" class="col-sm-2 col-form-label">Visi</label>
        <div class="col-sm-10">
            <textarea id="summernote1" class="form-control summernote-textarea" name="vision"
                id="vision">{{ old('vision', $companyInfo->vision ?? '') }}</textarea>
        </div>
    </div>
    <div class="form-group row">
        <label for="mission" class="col-sm-2 col-form-label">Visi</label>
        <div class="col-sm-10">
            <textarea id="summernote2" class="form-control summernote-textarea" name="mission"
                id="mission">{{ old('mission', $companyInfo->mission ?? '') }}</textarea>
        </div>
    </div>
    <div class="form-group row">
        <label for="address" class="col-sm-2 col-form-label">Visi</label>
        <div class="col-sm-10">
            <textarea id="summernote3" class="form-control summernote-textarea " name="address"
                id="address">{{ old('address', $companyInfo->address ?? '') }}</textarea>
        </div>
    </div>

    <div class="form-group row">
        <label for="email" class="col-sm-2 col-form-label">Email</label>
        <div class="col-sm-10">
            <input type="email" class="form-control" id="email" placeholder="Email" name="email"
                value="{{ old('email', $companyInfo->email ?? '') }}">
        </div>
    </div>
    <div class="form-group row">
        <label for="phone_number" class="col-sm-2 col-form-label">Number HandPhone</label>
        <div class="col-sm-10">
            <input type="phone_number" class="form-control" id="phone_number" placeholder="phone_number"
                name="phone_number" value="{{ old('phone_number', $companyInfo->phone_number ?? '') }}">
        </div>
    </div>
    <div class="form-group row">
        <label for="phone_number" class="col-sm-2 col-form-label">Logo</label>
        <div class="col-sm-10">
            <input type="file" name="logo" accept="image/*" onchange="preview('.tampil-gambar', this.files[0])">
            <!-- Elemen untuk menampilkan gambar preview -->
            <br>
            <div class="tampil-gambar">
                <!-- Jika ada gambar lama, tampilkan -->
                @if (isset($companyInfo->logo))
                <img src="{{ asset($companyInfo->logo) }}" alt="Gambar" class="img-thumbnail" width="100">
                @endif
            </div>
        </div>
    </div>
    <div class="form-group row">
        <div class="offset-sm-2 col-sm-10">
            <button type="submit" class="btn btn-danger">Update</button>
        </div>
    </div>
</form>