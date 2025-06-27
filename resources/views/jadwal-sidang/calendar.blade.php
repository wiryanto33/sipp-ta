@extends('layouts.dashboard')

@section('title', 'Kalender Jadwal Sidang')

@section('content')
    <div class="container mt-4">
        <h4 class="mb-4"><i class="fas fa-calendar-alt"></i> Kalender Jadwal Sidang</h4>
        <div class="calendar-wrapper">
            <div id="calendar"></div>
        </div>
    </div>


    @push('scripts')
        <style>
            .calendar-wrapper {
                max-width: 900px;
                /* Atur sesuai keinginan, misal 80%, 700px, dll */
                margin: auto;
            }

            #calendar {
                font-size: 0.9rem;
                /* Ukuran teks di dalam kalender */
            }

            /* Responsive untuk layar kecil */
            @media (max-width: 768px) {
                .calendar-wrapper {
                    max-width: 100%;
                    padding: 0 10px;
                }

                #calendar {
                    font-size: 0.75rem;
                }
            }
        </style>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const calendarEl = document.getElementById('calendar');
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,listWeek'
                    },
                    events: [
                        @foreach ($jadwalSidangs as $jadwal)
                            @php
                                $mahasiswaNama = optional($jadwal->tugasAkhir->mahasiswa)->name ?? 'Mahasiswa Tidak Diketahui';
                                $color = match ($jadwal->jenis_sidang) {
                                    'proposal' => '#17a2b8',
                                    'skripsi' => '#28a745',
                                    'tugas_akhir' => '#ffc107',
                                    default => '#6c757d',
                                };
                            @endphp {
                                title: @json($jadwal->jenis_sidang . ' - ' . $mahasiswaNama),
                                start: @json(\Carbon\Carbon::parse($jadwal->tanggal_sidang)->toDateString()),
                                url: @json(route('jadwal-sidang.show', $jadwal->id)),
                                color: @json($color),
                            },
                        @endforeach
                    ],
                    eventClick: function(info) {
                        info.jsEvent.preventDefault(); // Prevent default browser behavior
                        if (info.event.url) {
                            window.open(info.event.url, "_blank");
                        }
                    }
                });

                calendar.render();
            });
        </script>
    @endpush
@endsection
