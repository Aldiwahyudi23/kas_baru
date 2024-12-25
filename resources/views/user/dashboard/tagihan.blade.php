            <div class="card bg-warning text-white shadow">
                <center>
                    <h5>
                        Tagihan Aktif (Atas Nama)<br>
                        Rp {{ number_format($total ?? 0, 0, ',', '.') }}
                    </h5>
                </center>
                <a href="javascript:void(0)" id="toggleTagihan" class="card-footer text-white clearfix small z-1">
                    <span class="float-left">Lihat Tagihan</span>
                    <span class="float-right">
                        <i class="fas fa-angle-down"></i>
                    </span>
                </a>

                <!-- Section to show loan and konter data -->
                <div id="tagihanDetails" style="display: none;">
                    <ul>
                        <!-- Tampilkan pinjaman -->
                        @foreach($pinjaman as $loan)
                        <li style="display: flex; justify-content: space-between;">
                            <span>
                                Sisa Pinjaman {{$loan->Warga->name}} Rp
                                {{ number_format($loan->remaining_balance, 0, ',', '.') }}
                            </span>
                            <span style="margin-left: 20px; padding-right: 10px; text-align: right;">
                                {{ round(\Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($loan->deadline_date), false)) }}
                                hari lagi
                            </span>
                        </li>
                        @endforeach

                        <!-- Tampilkan konter -->
                        @foreach($konter as $data)
                        <li style="display: flex; justify-content: space-between;">
                            <span>
                                {{$data->product->kategori->name}} {{$data->product->provider->name}}
                                {{$data->detail->name}} Rp
                                {{ number_format($data->invoice, 0, ',', '.') }}
                            </span>
                            <span style="margin-left: 20px; padding-right: 10px; text-align: right;">
                                {{ round(\Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($data->deadline_date), false)) }}
                                hari lagi
                            </span>
                        </li>
                        @endforeach
                    </ul>
                </div>

            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const toggleTagihan = document.getElementById('toggleTagihan');
                    const tagihanDetails = document.getElementById('tagihanDetails');

                    toggleTagihan.addEventListener('click', function() {
                        if (tagihanDetails.style.display === 'none' || tagihanDetails.style.display === '') {
                            tagihanDetails.style.display = 'block';
                            this.querySelector('.fas').classList.remove('fa-angle-down');
                            this.querySelector('.fas').classList.add('fa-angle-up');
                        } else {
                            tagihanDetails.style.display = 'none';
                            this.querySelector('.fas').classList.remove('fa-angle-up');
                            this.querySelector('.fas').classList.add('fa-angle-down');
                        }
                    });
                });
            </script>