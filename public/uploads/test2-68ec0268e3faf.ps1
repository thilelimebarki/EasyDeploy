# -------------------------------------------------
# Script de test pour EasyDeploy
# -------------------------------------------------

# Log file
$logPath = "$PSScriptRoot\install_log.txt"

try {
    # Write in log
    "Starting installation test..." | Out-File -FilePath $logPath -Append
    Write-Host "✅ Script started successfully."
    
    # Simulate action
    Start-Sleep -Seconds 2
    "Action executed..." | Out-File -FilePath $logPath -Append
    Write-Host "🔧 Test action executed."
    
    # End log
    "Script finished successfully." | Out-File -FilePath $logPath -Append
    Write-Host "🎉 Script finished successfully."

} catch {
    # Log error
    "Error during script execution: $_" | Out-File -FilePath $logPath -Append
    Write-Host "❌ An error occurred during the test."
}
