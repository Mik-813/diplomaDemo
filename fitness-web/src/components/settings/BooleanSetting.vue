<script setup lang="ts">
import SwitchToggle from '$src/components/inputs/SwitchToggle.vue'
import ParameterTemplate from '$src/components/settings/SettingLayout.vue'

defineProps<{
  title: string
  subtitle: string
  state?: boolean
  onToggle: (active: boolean) => void
}>()
</script>

<template>
  <ParameterTemplate
    :title="title"
    :subtitle="subtitle"
  >
    <SwitchToggle 
      :active="state"
      @toggle="onToggle"
    />
  </ParameterTemplate>
</template>
