<template>
  <div class="page-header">
    <div class="page-block">
      <div class="row align-items-center">
        <div class="col-md-12">
          <div class="page-header-title">
            <h5 class="m-b-10">{{ pageTitle }}</h5>
          </div>
          <ul class="breadcrumb">
            <!-- Home link -->
            <li class="breadcrumb-item">
              <a href="/admin">Home</a>
            </li>
            
            <!-- Dynamic breadcrumb items -->
            <li 
              v-for="(item, index) in items" 
              :key="index"
              class="breadcrumb-item"
              :class="{ 'active': index === items.length - 1 }"
              :aria-current="index === items.length - 1 ? 'page' : null"
            >
              <a 
                v-if="item.url && index !== items.length - 1" 
                :href="item.url"
              >
                {{ item.text }}
              </a>
              <span v-else>{{ item.text }}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Breadcrumb',
  props: {
    pageTitle: {
      type: String,
      default: 'Dashboard'
    },
    items: {
      type: Array,
      default: () => []
    }
  }
}
</script>

<style scoped>
/* Let the admin template handle breadcrumb styling */
</style>