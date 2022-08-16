<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Events</h2>
    </template>

    <template #flash-message>
      <v-app id="message-block-app">
        <v-alert  v-if="$page.props.flash.success && show" type="success" close-text="Close Alert" dismissible>
          {{ $page.props.flash.success }}
        </v-alert>
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
    };
  },
  watch: {
    mutableFilter: {
      deep: true,
      handler: throttle(function () {
        this.$inertia.get("/events", pickBy(this.mutableFilter), {
          preserveState: true,
        });
      }, 150),
    },
    watch: {
      "$page.props.flash": {
        handler() {
          this.show = true;
        },
        deep: true,
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
    fetchEvents({ start, end }) {
      const min = moment(`${start.date}T00:00:00`).format(
        "YYYY-MM-DD HH:mm:ss"
      );
      const max = moment(`${end.date}T23:59:59`).format("YYYY-MM-DD HH:mm:ss");

      this.$inertia.get(
        "/events",
        {
          start_date: min,
          end_date: max,
          //   keywords: this.mutableFilter.keywords,
        },
        {
          preserveState: true,
        }
      );
    },
    showEditingModal(e) {
      this.$root.$emit("show-editing-modal", e.event ? e.event : null);
    },
  },
};
</script>

<style>
#message-block-app {
  background-color: transparent !important;
}

#message-block-app > .v-application--wrap {
  min-height: min-content !important;
}
</style>