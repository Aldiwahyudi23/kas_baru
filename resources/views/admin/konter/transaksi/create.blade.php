            <div class="card-body">
                <form action="{{ route('konter-transaksi.store') }}" method="POST" enctype="multipart/form-data" id="adminForm">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-sm-6">

                            <div class="form-group">
                                <label for="no_hp">No HandPhone <span class="text-danger">*</span></label>
                                <input type="number" name="no_hp" id="no_hp" value="{{old('no_hp')}}" class="form-control col-12 @error('name') is-invalid @enderror">
                            </div>

                            <div>
                                <label for="provider">Provider:</label>
                                <input type="text" id="provider" name="provider" readonly>
                            </div>
                            <div>
                                <label for="amount">Pilih Nominal:</label>
                                <select id="amount" name="product_id" required>
                                    <option value="">Pilih nominal</option>
                                </select>
                            </div>
                            <div>
                                <label for="price">Harga Jual:</label>
                                <input type="text" id="price" name="price" readonly>
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

            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const noHpInput = document.getElementById("no_hp");
                    const providerInput = document.getElementById("provider");
                    const amountSelect = document.getElementById("amount");
                    const priceInput = document.getElementById("price");
                    const pulsaForm = document.getElementById("pulsaForm");

                    // Event listener untuk cek nomor HP
                    noHpInput.addEventListener("blur", async () => {
                        const noHp = noHpInput.value;

                        if (!noHp) {
                            alert("Masukkan nomor HP!");
                            return;
                        }

                        // Fetch data dari server
                        try {
                            const response = await fetch("/check-phone", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({
                                    no_hp: noHp
                                })
                            });

                            const data = await response.json();

                            if (data.success) {
                                providerInput.value = data.layanan;
                                populateAmount(data.products);
                            } else {
                                alert(data.message);
                                providerInput.value = "";
                                amountSelect.innerHTML = '<option value="">Pilih nominal</option>';
                                priceInput.value = "";
                            }
                        } catch (error) {
                            console.error("Error:", error);
                            alert("Terjadi kesalahan saat memproses data.");
                        }
                    });

                    // Populasi dropdown amount
                    function populateAmount(products) {
                        amountSelect.innerHTML = '<option value="">Pilih nominal</option>';
                        products.forEach(product => {
                            const option = document.createElement("option");
                            option.value = product.id; // Menggunakan ID produk
                            option.textContent = `${product.amount}`;
                            option.dataset.price = product.amount * 1.1; // Contoh markup 10% untuk harga jual
                            amountSelect.appendChild(option);
                        });
                    }

                    // Update harga jual ketika nominal dipilih
                    amountSelect.addEventListener("change", function() {
                        const selectedOption = amountSelect.options[amountSelect.selectedIndex];
                        const price = selectedOption.dataset.price || "";
                        priceInput.value = price;
                    });

                    // Handle submit form
                    pulsaForm.addEventListener("submit", async function(event) {
                        event.preventDefault();

                        const formData = {
                            no_hp: noHpInput.value,
                            product_id: amountSelect.value,
                            price: priceInput.value
                        };

                        if (!formData.product_id) {
                            alert("Pilih nominal terlebih dahulu!");
                            return;
                        }

                        try {
                            const response = await fetch("/transaksi/store", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify(formData)
                            });

                            const data = await response.json();

                            if (data.success) {
                                alert("Transaksi berhasil disimpan!");
                                pulsaForm.reset();
                                providerInput.value = "";
                                amountSelect.innerHTML = '<option value="">Pilih nominal</option>';
                                priceInput.value = "";
                            } else {
                                alert("Gagal menyimpan transaksi: " + data.message);
                            }
                        } catch (error) {
                            console.error("Error:", error);
                            alert("Terjadi kesalahan saat menyimpan transaksi.");
                        }
                    });
                });
            </script>