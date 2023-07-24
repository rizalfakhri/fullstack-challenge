<script>
export default {
  data: () => ({
    apiResponse: null,
  }),

  created() {
    this.fetchData();
  },

  methods: {
    async fetchData() {
      const url = "http://localhost";
      let response = await (await fetch(url)).json();

      this.apiResponse = response.data.map((weatherReport) => {
        return {
          ...weatherReport,
          showForecast: false,
        };
      });
    },
  },
};
</script>

<template>
  <div
    class="p-4 mb-20 border border-gray-200 rounded shadow md:p-6 dark:border-gray-700"
  >
    <div v-if="!apiResponse">
      <div
        role="status"
        class="p-4 animate-pulse divide-y divide-gray-700 space-y-4"
      >
        <div class="flex items-center justify-between">
          <div>
            <div
              class="h-2.5 bg-gray-300 rounded-full dark:bg-gray-600 w-24 mb-2.5"
            ></div>
            <div
              class="w-32 h-2 bg-gray-200 rounded-full dark:bg-gray-700"
            ></div>
          </div>
          <div
            class="h-2.5 bg-gray-300 rounded-full dark:bg-gray-700 w-12"
          ></div>
        </div>
        <div class="flex items-center justify-between pt-4">
          <div>
            <div
              class="h-2.5 bg-gray-300 rounded-full dark:bg-gray-600 w-24 mb-2.5"
            ></div>
            <div
              class="w-32 h-2 bg-gray-200 rounded-full dark:bg-gray-700"
            ></div>
          </div>
          <div
            class="h-2.5 bg-gray-300 rounded-full dark:bg-gray-700 w-12"
          ></div>
        </div>
        <div class="flex items-center justify-between pt-4">
          <div>
            <div
              class="h-2.5 bg-gray-300 rounded-full dark:bg-gray-600 w-24 mb-2.5"
            ></div>
            <div
              class="w-32 h-2 bg-gray-200 rounded-full dark:bg-gray-700"
            ></div>
          </div>
          <div
            class="h-2.5 bg-gray-300 rounded-full dark:bg-gray-700 w-12"
          ></div>
        </div>
        <div class="flex items-center justify-between pt-4">
          <div>
            <div
              class="h-2.5 bg-gray-300 rounded-full dark:bg-gray-600 w-24 mb-2.5"
            ></div>
            <div
              class="w-32 h-2 bg-gray-200 rounded-full dark:bg-gray-700"
            ></div>
          </div>
          <div
            class="h-2.5 bg-gray-300 rounded-full dark:bg-gray-700 w-12"
          ></div>
        </div>
        <div class="flex items-center justify-between pt-4">
          <div>
            <div
              class="h-2.5 bg-gray-300 rounded-full dark:bg-gray-600 w-24 mb-2.5"
            ></div>
            <div
              class="w-32 h-2 bg-gray-200 rounded-full dark:bg-gray-700"
            ></div>
          </div>
          <div
            class="h-2.5 bg-gray-300 rounded-full dark:bg-gray-700 w-12"
          ></div>
        </div>
        <span class="sr-only">Loading...</span>
      </div>
    </div>

    <div v-if="apiResponse">
      <div class="divide-y divide-gray-700 space-y-20">
        <div
          class="pt-4"
          v-for="(data, index) in apiResponse"
          v-bind:key="index"
        >
          <div v-if="data.weather_report.length < 1">
            <div class="p-5 border-2 border-red-500 border-dashed rounded">
              <h4 class="font-light text-center">
                Unable to get the weather report for {{ data.name }}
              </h4>
            </div>
          </div>
          <div v-else class="pt-4 space-y-5">
            <div class="flex items-center justify-between w-full">
              <span class="text-gray-300">
                {{ data.name }}'s Weather Report
              </span>
              <div
                class="flex items-center text-gray-300 space-x-2"
                v-if="data.weather_report.hasOwnProperty('location')"
              >
                <svg
                  width="24"
                  height="24"
                  xmlns="http://www.w3.org/2000/svg"
                  fill-rule="evenodd"
                  clip-rule="evenodd"
                  fill="currentColor"
                >
                  <path
                    d="M12 10c-1.104 0-2-.896-2-2s.896-2 2-2 2 .896 2 2-.896 2-2 2m0-5c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3m-7 2.602c0-3.517 3.271-6.602 7-6.602s7 3.085 7 6.602c0 3.455-2.563 7.543-7 14.527-4.489-7.073-7-11.072-7-14.527m7-7.602c-4.198 0-8 3.403-8 7.602 0 4.198 3.469 9.21 8 16.398 4.531-7.188 8-12.2 8-16.398 0-4.199-3.801-7.602-8-7.602"
                  />
                </svg>
                <span
                  >{{ data.weather_report.location.city }},
                  {{ data.weather_report.location.country }}</span
                >
              </div>
            </div>

            <div class="flex flex-col">
              <span class="text-gray-200 opacity-50">Condition</span>
              <h4 class="text-2xl font-bold text-gray-200">
                {{ data.weather_report.condition
                }}<span v-if="data.weather_report.condition_description"
                  >,&nbsp;</span
                >
                <span
                  class="text-sm font-light opacity-40"
                  v-if="data.weather_report.condition_description"
                  >{{ data.weather_report.condition_description }}</span
                >
              </h4>
            </div>

            <div class="grid gap-2 grid-cols-3 grid-rows-1">
              <div class="h-auto p-2 border rounded border-secondary">
                <div class="flex p-3">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-10 h-10"
                    fill="currentColor"
                  >
                    <path
                      d="M4.069 13h-4.069v-2h4.069c-.041.328-.069.661-.069 1s.028.672.069 1zm3.034-7.312l-2.881-2.881-1.414 1.414 2.881 2.881c.411-.529.885-1.003 1.414-1.414zm11.209 1.414l2.881-2.881-1.414-1.414-2.881 2.881c.528.411 1.002.886 1.414 1.414zm-6.312-3.102c.339 0 .672.028 1 .069v-4.069h-2v4.069c.328-.041.661-.069 1-.069zm0 16c-.339 0-.672-.028-1-.069v4.069h2v-4.069c-.328.041-.661.069-1 .069zm7.931-9c.041.328.069.661.069 1s-.028.672-.069 1h4.069v-2h-4.069zm-3.033 7.312l2.88 2.88 1.415-1.414-2.88-2.88c-.412.528-.886 1.002-1.415 1.414zm-11.21-1.415l-2.88 2.88 1.414 1.414 2.88-2.88c-.528-.411-1.003-.885-1.414-1.414zm2.312-4.897c0 2.206 1.794 4 4 4s4-1.794 4-4-1.794-4-4-4-4 1.794-4 4zm10 0c0 3.314-2.686 6-6 6s-6-2.686-6-6 2.686-6 6-6 6 2.686 6 6z"
                    />
                  </svg>
                  <span class="text-gray-200 text-md">Temperature</span>
                </div>
                <div class="flex px-3 space-x-3">
                  <template
                    v-for="(temp, index) in data.weather_report.temperatures"
                    v-bind:key="`temp-${index}`"
                  >
                    <h3 class="font-bold" v-html="temp"></h3>
                    <h3
                      class="text-gray-200 opacity-20"
                      v-if="data.weather_report.temperatures.length - 1 > index"
                    >
                      /
                    </h3>
                  </template>
                </div>
              </div>
              <div
                class="h-auto p-2 border rounded border-secondary col-span-2"
              >
                <div class="flex p-3">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor"
                    class="w-10 h-10 text-gray-200"
                  >
                    <path
                      d="M11 10h-11v-2h11c.552 0 1-.448 1-1s-.448-1-1-1c-.403 0-.747.242-.905.587l-1.749-.956c.499-.965 1.494-1.631 2.654-1.631 3.971 0 3.969 6 0 6zm7 7c0-1.656-1.344-3-3-3h-15v2h15c.552 0 1 .448 1 1s-.448 1-1 1c-.403 0-.747-.242-.905-.587l-1.749.956c.499.965 1.494 1.631 2.654 1.631 1.656 0 3-1.344 3-3zm1.014-7.655c.082-.753.712-1.345 1.486-1.345.827 0 1.5.673 1.5 1.5s-.673 1.5-1.5 1.5h-20.5v2h20.5c1.932 0 3.5-1.568 3.5-3.5s-1.568-3.5-3.5-3.5c-1.624 0-2.977 1.116-3.372 2.617l1.886.728z"
                    />
                  </svg>
                  <span class="text-gray-200 text-md">Wind</span>
                </div>

                <div class="flex px-3 space-x-3">
                  <h3 class="font-bold" v-if="data.weather_report.wind.speed">
                    {{ data.weather_report.wind.speed }}
                    <span class="text-sm font-light text-gray-200/40"
                      >Speed</span
                    >
                  </h3>
                  <template
                    v-if="
                      data.weather_report.wind.direction &&
                      data.weather_report.wind.degree
                    "
                  >
                    <h3 class="text-gray-200 opacity-20">/</h3>
                    <h3 class="font-bold">
                      {{ data.weather_report.wind.direction }} @
                      {{ data.weather_report.wind.degree }}&deg;
                      <span class="text-sm font-light text-gray-200/40"
                        >Direction</span
                      >
                    </h3>
                  </template>
                  <template v-if="data.weather_report.wind.gust">
                    <h3 class="text-gray-200 opacity-20">/</h3>
                    <h3 class="font-bold">
                      {{ data.weather_report.wind.gust }}
                      <span class="text-sm font-light text-gray-200/40"
                        >Gust</span
                      >
                    </h3>
                  </template>
                </div>
              </div>
            </div>

            <div class="relative forecast">
              <div
                class="absolute z-20 w-[105%] -ml-5 mt-10 h-full bg-gradient-to-b from-transparent to-40% to-primary"
                v-if="!data.weather_report.showForecast"
              >
                <div
                  class="absolute top-0 bottom-0 flex items-center justify-center w-full"
                >
                  <button
                    class="px-3 py-2 text-white rounded bg-gray-800/40"
                    @click="data.weather_report.showForecast = true"
                  >
                    Show Forecast
                  </button>
                </div>
              </div>

              <div
                class="relative z-10 overflow-x-auto shadow-md sm:rounded-lg"
              >
                <table class="w-full text-sm text-left text-gray-400">
                  <caption
                    class="p-5 text-lg font-semibold text-left text-white bg-secondary"
                  >
                    Forecast
                    <p class="mt-1 text-sm font-normaltext-gray-400">
                      Below is the forecast report for {{ data.name }} on
                      {{ data.weather_report.location.city }},
                      {{ data.weather_report.location.country }}
                    </p>
                  </caption>
                  <thead class="text-xs text-gray-400 uppercase bg-primary">
                    <tr>
                      <th scope="col" class="px-6 py-3">Date & Time</th>
                      <th scope="col" class="px-6 py-3">Condition</th>
                      <th scope="col" class="px-6 py-3">Temperature</th>
                      <th scope="col" class="px-6 py-3">Wind</th>
                    </tr>
                  </thead>
                  <tbody v-if="!data.weather_report.showForecast">
                    <tr class="bg-white border-b border-secondary bg-secondary">
                      <th
                        scope="row"
                        class="px-6 py-4 font-medium text-white whitespace-nowrap"
                      >
                        PLACEHOLDER
                      </th>
                      <td class="px-6 py-4">PLACEHOLDER</td>
                      <td class="px-6 py-4">PLACEHOLDER</td>
                      <td class="px-6 py-4">PLACEHOLDER</td>
                    </tr>
                  </tbody>
                  <tbody v-else>
                    <tr
                      v-for="(forecast, index) in data.weather_report.forecast"
                      v-bind:key="index"
                      class="bg-white border-b border-secondary bg-secondary"
                    >
                      <td
                        scope="row"
                        class="px-6 py-4 font-medium text-white whitespace-nowrap"
                      >
                        {{ forecast.forecasted_date }}
                      </td>
                      <td class="px-6 py-4">
                        {{ forecast.forecasted_weather.condition }}
                      </td>
                      <td class="px-6 py-4">
                        <template
                          v-for="(temp, tempIndex) in forecast
                            .forecasted_weather.temperatures"
                          v-bind:key="`temp-forecast-${index}-${tempIndex}`"
                        >
                          <span class="font-bold" v-html="temp"></span>
                          <span
                            class="text-gray-200 opacity-20"
                            v-if="
                              forecast.forecasted_weather.temperatures.length -
                                1 >
                              tempIndex
                            "
                          >
                            /
                          </span>
                        </template>
                      </td>
                      <td class="px-6 py-4">
                        <span
                          class="font-bold"
                          v-if="forecast.forecasted_weather.wind.speed"
                        >
                          {{ forecast.forecasted_weather.wind.speed }}
                        </span>
                        &nbsp;
                        <template
                          v-if="
                            forecast.forecasted_weather.wind.direction &&
                            forecast.forecasted_weather.wind.degree
                          "
                        >
                          <span class="font-bold">
                            {{ forecast.forecasted_weather.wind.direction }} @
                            {{ forecast.forecasted_weather.wind.degree }}&deg;
                          </span>
                        </template>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div
                class="flex items-center justify-center w-full mt-5"
                v-if="data.weather_report.showForecast"
              >
                <button
                  class="px-3 py-2 text-white rounded bg-gray-800/40"
                  @click="data.weather_report.showForecast = false"
                >
                  Hide Forecast
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
