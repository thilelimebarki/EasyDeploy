chcp 65001 > $null
[Console]::OutputEncoding = [System.Text.Encoding]::UTF8
[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12
[System.Net.ServicePointManager]::ServerCertificateValidationCallback = {$true}

$TempFolder = "$env:TEMP\NotepadPP_Install"
$InstallerPath = "$TempFolder\npp_installer.exe"
$LogPath = "$TempFolder\install_log.txt"

if (!(Test-Path -Path $TempFolder)) {
    New-Item -ItemType Directory -Path $TempFolder | Out-Null
}

function Write-Log($message) {
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    "$timestamp - $message" | Out-File -FilePath $LogPath -Append
    Write-Host $message
}

try {
    Write-Log "Démarrage de l'installation de Notepad++..."
    $url = "https://github.com/notepad-plus-plus/notepad-plus-plus/releases/latest/download/npp.8.6.7.Installer.x64.exe"
    Write-Log "Téléchargement depuis $url"

    Invoke-WebRequest -Uri $url -OutFile $InstallerPath -UseBasicParsing

    if (Test-Path $InstallerPath) {
        Write-Log "Téléchargement terminé avec succès."
        Write-Log "Installation silencieuse en cours..."
        Start-Process -FilePath $InstallerPath -ArgumentList "/S" -Wait
        Write-Log "Installation terminée avec succès."
    } else {
        Write-Log "Échec du téléchargement du fichier d'installation."
    }
}
catch {
    Write-Log "Erreur pendant l'installation : $($_.Exception.Message)"
    Write-Host "Une erreur est survenue : $($_.Exception.Message)"
}
finally {
    Write-Log "Fin du processus d'installation."
    Write-Host "Fin du processus d'installation."
    Pause
}
