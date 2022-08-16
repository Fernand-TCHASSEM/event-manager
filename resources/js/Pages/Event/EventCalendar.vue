<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Events</h2>
    </template>

    <template #additionnal-block>
      <v-app id="additionnal-block-app">
        <v-alert
          v-if="$page.props.flash.success && show"
          type="success"
          close-text="Close Alert"
          dismissible
        >
          {{ $page.props.flash.success }}
        </v-alert>

        <v-card elevation="1">
          <v-card-title>
            <v-row>
              <v-col cols="4" class="self-end">Filters</v-col>
              <v-col cols="3" offset="1">
                <v-menu
                  ref="menu"
                  v-model="menu"
                  :close-on-content-click="false"
                  :return-value.sync="dates"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      v-model="dateRangeText"
                      label="Date range"
                      append-icon="mdi-calendar"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                      single-line
                      hide-details
                    ></v-text-field>
                  </template>
                  <v-date-picker v-model="dates" range>
                    <v-spacer></v-spacer>
                    <v-btn text color="primary" @click="menu = false">
                      Cancel
                    </v-btn>
                    <v-btn text color="primary" @click="$refs.menu.save(dates)">
                      OK
                    </v-btn>
                  </v-date-picker>
                </v-menu>
              </v-col>
              <v-col cols="3">
                <v-text-field
                  v-model="mutableFilter.keywords"
                  append-icon="mdi-pencil"
                  label="Search"
                  single-line
                  hide-details
                ></v-text-field>
              </v-col>
              <v-col cols="1" class="self-end">
                <v-btn
                  class="mx-2"
                  fab
                  dark
                  small
                  color="primary"
                  @click="fetchEvents"
                >
                  <v-icon>mdi-magnify</v-icon>
                </v-btn>
              </v-col>
            </v-row>
          </v-card-title>
        </v-card>
      </v-app>
    </template>

    <div class="pt-6 pb-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <v-app id="inspire">
            <v-row class="fill-height">
              <v-col>
                <v-sheet height="64">
                  <v-toolbar flat>
                    <v-btn
                      outlined
                      class="mr-4"
                      color="grey darken-2"
                      @click="setToday"
                    >
                      Today
                    </v-btn>
                    <v-btn fab text small color="grey darken-2" @click="prev">
                      <v-icon small> mdi-chevron-left </v-icon>
                    </v-btn>
                    <v-btn fab text small color="grey darken-2" @click="next">
                      <v-icon small> mdi-chevron-right </v-icon>
                    </v-btn>
                    <v-toolbar-title v-if="$refs.calendar">
                      {{ $refs.calendar.title }}
                    </v-toolbar-title>
                    <v-btn
                      color="primary"
                      dark
                      class="m-auto"
                      @click="showEditingModal"
                      >Add an event</v-btn
                    >

                    <!-- Modal for editing an event -->
                    <edit-event />

                    <v-menu bottom right>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          outlined
                          color="grey darken-2"
                          class="ml-auto"
                          v-bind="attrs"
                          v-on="on"
                        >
                          <span>{{ typeToLabel[type] }}</span>
                          <v-icon right> mdi-menu-down </v-icon>
                        </v-btn>
                      </template>
                      <v-list>
                        <v-list-item @click="type = 'day'">
                          <v-list-item-title>Day</v-list-item-title>
                        </v-list-item>
                        <v-list-item @click="type = 'week'">
                          <v-list-item-title>Week</v-list-item-title>
                        </v-list-item>
                        <v-list-item @click="type = 'month'">
                          <v-list-item-title>Month</v-list-item-title>
                        </v-list-item>
                        <v-list-item @click="type = '4day'">
                          <v-list-item-title>4 days</v-list-item-title>
                        </v-list-item>
                      </v-list>
                    </v-menu>
                  </v-toolbar>
                </v-sheet>
                <v-sheet height="600">
                  <v-calendar
                    ref="calendar"
                    v-model="focus"
                    :start="calendarStartDate"
                    color="primary"
                    :events="events"
                    :event-color="getEventColor"
                    :type="type"
                    @click:event="showEditingModal"
                    @click:more="viewDay"
                    @click:date="viewDay"
                    @change="fetchEvents"
                  ></v-calendar>
                </v-sheet>
              </v-col>
            </v-row>
          </v-app>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import pickBy from "lodash/pickBy";
import throttle from "lodash/throttle";
import moment from "moment";

import AppLayout from "@/Layouts/AppLayout";
import EditEvent from "./EditEvent";

export default {
  components: {
    AppLayout,
    EditEvent,
  },
  props: {
    filters: Object,
    events: Array,
  },
  data() {
    return {
      show: true,
      focus: "",
      calendarStartDate: this.filters.start_date,
      type: "month",
      typeToLabel: {
        month: "Month",
        week: "Week",
        day: "Day",
        "4day": "4 Days",
      },
      mutableFilter: {
        startDate: this.filters.start_date,
        endDate: this.filters.end_date,
        keywords: this.filters.keywords,
      },
      dates: [this.filters.start_date, this.filters.end_date],
      menu: false,
    };
  },
  computed: {
    dateRangeText() {
      return (
        moment(this.mutableFilter.startDate).format("YYYY-MM-DD") +
        " ~ " +
        moment(this.mutableFilter.endDate).format("YYYY-MM-DD")
      );
    },
  },
  watch: {
    mutableFilter: {
      deep: true,
      handler(val) {
        this.dates[0] = val.startDate;
        this.dates[1] = val.endDate;
      },
    },
    "$page.props.flash": {
      deep: true,
      handler() {
        this.show = true;
      },
    },
  },
  mounted() {
    this.$refs.calendar.checkChange();
  },
  methods: {
    viewDay({ date }) {
      this.focus = date;
      this.type = "day";
    },
    getEventColor(event) {
      return event.color;
    },
    setToday() {
      this.focus = "";
    },
    prev() {
      this.$refs.calendar.prev();
    },
    next() {
      this.$refs.calendar.next();
    },
    fetchEvents({ start = null, end = null }) {
      if (start) {
        this.mutableFilter.startDate = start.date;
        start = start.date + "T00:00:00";
      } else {
        start = this.dates[0] + " 00:00:00";
      }

      if (end) {
        this.mutableFilter.endDate = end.date;
        end = end.date + "T23:59:59";
      } else {
        end = this.dates[1] + " 23:59:59";
      }

      var data = {
        start_date: moment(start).format("YYYY-MM-DD HH:mm:ss"),
        end_date: moment(end).format("YYYY-MM-DD HH:mm:ss"),
        keywords: this.mutableFilter.keywords,
      };

      this.$inertia.get("/events", data, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
          this.calendarStartDate = data.start_date;
        },
      });
    },
    showEditingModal(e) {
      this.$root.$emit("show-editing-modal", e.event ? e.event : null);
    },
  },
};
</script>

<style>
#additionnal-block-app {
  background-color: transparent !important;
}

#additionnal-block-app > .v-application--wrap {
  min-height: min-content !important;
}
</style>