<template>
  <app-layout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Dashboard
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <v-app id="inspire">
          <div>
            <v-alert class="mb-0" border="top" colored-border type="info" elevation="2">
              To see the calendar of events, please click on the link
              <jet-nav-link :href="route('events.index')">Events</jet-nav-link>
            </v-alert>
          </div>
        </v-app>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import JetNavLink from "@/Jetstream/NavLink";

export default {
  components: {
    AppLayout,
    JetNavLink,
  },
};
</script>

<style>
.v-application--wrap {
    min-height: auto !important;
}
</style>