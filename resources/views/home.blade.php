<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Upcoming weather') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div x-data="weather()"
                     class="p-6 bg-white border-b border-gray-200">
                    <div class="flex items-center">
                        <select x-model="city" @change="getWeather()"
                                class="flex-1 ml-4 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">-- Select city --</option>
                            @foreach(config('app.cities') as $key => $name)
                                <option value="{{ $key }}">{{ Str::title($key) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <template x-if="loading">
                        <div class="loader bg-white p-5 my-4 rounded-full flex space-x-3 justify-center">
                            <div class="w-5 h-5 bg-red-800 rounded-full animate-bounce"></div>
                            <div class="w-5 h-5 bg-green-800 rounded-full animate-bounce"></div>
                            <div class="w-5 h-5 bg-blue-800 rounded-full animate-bounce"></div>
                        </div>
                    </template>

                    <template x-if="error != ''">
                        <div x-text="error" class="mt-4 text-red-600"></div>
                    </template>

                    <template x-if="!loading">
                        <div class="overflow-hidden overflow-x-auto mt-6 min-w-full align-middle sm:rounded-md">
                            <table class="min-w-full border divide-y divide-gray-200">
                                <thead>
                                <tr>
                                    <template x-for="day in weather.time">
                                        <th class="px-6 py-3 bg-gray-50">
                                        <span class="text-xs font-medium tracking-wider leading-4 text-left text-gray-500 uppercase"
                                              x-text="day"></span>
                                        </th>
                                    </template>
                                </tr>
                                </thead>

                                <tbody class="bg-white divide-y divide-gray-200 divide-solid">
                                <tr class="bg-white">
                                    <template x-for="max in weather.temperature_2m_max">
                                        <td class="px-6 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                                            Max. temp. <span x-text="max"></span>
                                        </td>
                                    </template>
                                </tr>
                                <tr class="bg-white">
                                    <template x-for="min in weather.temperature_2m_min">
                                        <td class="px-6 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                                            Min. temp. <span x-text="min"></span>
                                        </td>
                                    </template>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('weather', () => ({
                    city: '',
                    weather: {},
                    loading: false,
                    error: '',

                    getWeather() {
                        this.error = ''
                        this.weather = {}

                        if (this.city === '') {
                            return;
                        }

                        this.loading = true
                        fetch('/api/weather/' + this.city)
                            .then((res) => res.json())
                            .then((res) => {
                                if (!res.temperature_2m_max) {
                                    this.error = 'Error happened when fetching the API'
                                } else {
                                    this.weather = res
                                }
                                this.loading = false
                            })
                    }
                }))
            })
        </script>
    @endsection
</x-app-layout>

