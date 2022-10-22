<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laravel Analytics</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.css"
    />
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.js">
    </script>
</head>
<body>
<div class="min-h-screen bg-gray-100 text-gray-500 py-6 flex flex-col sm:py-16">
    <div class="px-4 w-full lg:px-0 sm:max-w-5xl sm:mx-auto">
        <div class="flex justify-end">
            @include('mailytics::components.filter-button')
        </div>
        <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-3">
            <div class="rounded-lg shadow-sm mb-4">
                <div class="rounded-lg bg-white shadow-lg md:shadow-xl relative overflow:hidden">
                    <div class="px-3 pt-8 pb-10 text-center relative z-10">
                        <h4 class="text-sm uppercase text-gray-500 leading-tight">
                            Sent Emails ✉️
                        </h4>
                        <h3 class="text-3xl text-gray-700 font-semibold leading-tight my-3">
                            {{$sent_emails['count']}}
                        </h3>
{{--                        <p class="text-xs text-green-500 leading-tight">--}}
{{--                            🔺 40.9%--}}
{{--                        </p>--}}
                    </div>
                    <div class="absolute bottom-0 inset-x-0">
                        <canvas id="sentEmails" height="70"></canvas>
                    </div>
                </div>
            </div>

            <div class="rounded-lg shadow-sm mb-4">
                <div class="rounded-lg bg-white shadow-lg md:shadow-xl relative overflow:hidden">
                    <div class="px-3 pt-8 pb-10 text-center relative z-10">
                        <h4 class="text-sm uppercase text-gray-500 leading-tight">
                            Open Rate 📨
                        </h4>
                        <h3 class="text-3xl text-gray-700 font-semibold leading-tight my-3">
                            {{$open_rate['percentage']}}%
                        </h3>
{{--                        <p class="text-xs text-green-500 leading-tight">--}}
{{--                            🔺 40.9%--}}
{{--                        </p>--}}
                    </div>
                    <div class="absolute bottom-0 inset-x-0">
                        <canvas id="openRate" height="70"></canvas>
                    </div>
                </div>
            </div>

            <div class="rounded-lg shadow-sm mb-4">
                <div class="rounded-lg bg-white shadow-lg md:shadow-xl relative overflow:hidden">
                    <div class="px-3 pt-8 pb-10 text-center relative z-10">
                        <h4 class="text-sm uppercase text-gray-500 leading-tight">
                            Open Rate 📨
                        </h4>
                        <h3 class="text-3xl text-gray-700 font-semibold leading-tight my-3">
                            {{$open_rate['percentage']}}%
                        </h3>
                        {{--                        <p class="text-xs text-green-500 leading-tight">--}}
                        {{--                            🔺 40.9%--}}
                        {{--                        </p>--}}
                    </div>
                    <div class="absolute bottom-0 inset-x-0">
                        <canvas id="openRate" height="70"></canvas>
                    </div>
                </div>
            </div>
        </dl>
    </div>
</div>

<script>
    const filterButton = document.getElementById('filter-button');
    const filterDropdown = document.getElementById('filter-dropdown');

    filterButton.addEventListener('click', function (e) {
        e.preventDefault();

        filterDropdown.style.display = 'block';
    });

    document.addEventListener('click', function (e) {
        if (!filterButton.contains(e.target) && !filterDropdown.contains(e.target)) {
            filterDropdown.style.display = 'none';
        }
    });
</script>
<script>
    const chartOptions = {
        maintainAspectRation: false,
        legend: {
            display: false
        },
        tooltips: {
            enable: false
        },
        elements: {
            point: {
                radius: 0
            }
        },
        scales: {
            xAxes: [
                {
                    gridLines: false,
                    scaleLabel: false,
                    ticks: {
                        display: false
                    }
                }
            ],
            yAxes: [
                {
                    gridLines: false,
                    scaleLabel: false,
                    ticks: {
                        display: false,
                        suggestedMin: 0,
                        suggestedMax: 10
                    }
                }
            ]
        }
    };

    const sentEmailElement = document.getElementById("sentEmails").getContext("2d");
    const sentEmailChart = new Chart(sentEmailElement, {
        type: "line",
        data: {
            labels: {{Illuminate\Support\Js::from($sent_emails['labels'])}},
            datasets: [
                {
                    backgroundColor: "rgba(101, 116, 205, 0.1)",
                    borderColor: "rgba(101, 116, 205, 0.8)",
                    borderWidth: 2,
                    data: {{Illuminate\Support\Js::from($sent_emails['data'])}}
                }
            ]
        },
        options: chartOptions
    });

    const openRateElement = document.getElementById("openRate").getContext("2d");
    const openRateChart = new Chart(openRateElement, {
        type: "line",
        data: {
            labels: {{Illuminate\Support\Js::from($open_rate['labels'])}},
            datasets: [
                {
                    backgroundColor: "rgba(101, 116, 205, 0.1)",
                    borderColor: "rgba(191, 52, 410, 0.8)",
                    borderWidth: 2,
                    data: {{Illuminate\Support\Js::from($open_rate['data'])}}
                }
            ]
        },
        options: chartOptions
    });
</script>
</body>
</html>
