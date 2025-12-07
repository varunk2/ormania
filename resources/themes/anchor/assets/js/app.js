// Register DataLabels plugin
Chart.register(ChartDataLabels);

// Global Chart Defaults
Chart.defaults.color = '#8B949E';
Chart.defaults.borderColor = '#30363D';
Chart.defaults.font.family = "'Inter', sans-serif";

window.demoButtonClickMessage = function(event){
    event.preventDefault(); new FilamentNotification().title('Modify this button in your theme folder').icon('heroicon-o-pencil-square').iconColor('info').send()
}
