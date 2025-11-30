# ==========================================
# EasyDeploy - Installation automatique Notepad++
# ==========================================

# Dossier temporaire
chcp 65001 > $null
[Console]::OutputEncoding = [System.Text.Encoding]::UTF8
[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12

$TempFolder = "$env:TEMP\NotepadPP_Install"
$InstallerPath = "$TempFolder\npp_installer.exe"
$LogPath = "$TempFolder\install_log.txt"

# Créer le dossier si inexistant
if (!(Test-Path -Path $TempFolder)) {
    New-Item -ItemType Directory -Path $TempFolder | Out-Null
}

# Fonction de log
function Write-Log($message) {
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    "$timestamp - $message" | Out-File -FilePath $LogPath -Append
    Write-Host $message
}

try {
    Write-Log " Démarrage de l'installation de Notepad++..."

    # Télécharger la dernière version de Notepad++
    $url = "https://github.com/notepad-plus-plus/notepad-plus-plus/releases/latest/download/npp.8.6.7.Installer.x64.exe"
    Write-Log "Téléchargement depuis $url"
    Invoke-WebRequest -Uri $url -OutFile $InstallerPath

    if (Test-Path $InstallerPath) {
        Write-Log " Téléchargement terminé avec succès."

        # Lancer l’installation silencieuse
        Write-Log "Installation silencieuse en cours..."
        Start-Process -FilePath $InstallerPath -ArgumentList "/S" -Wait

        # Vérifier si Notepad++ est installé
        $nppPath = "C:\Program Files\Notepad++\notepad++.exe"
        if (Test-Path $nppPath) {
            Write-Log "Installation réussie : Notepad++ est installé."
            Write-Host "🎉 Installation terminée avec succès !"
        } else {
            Write-Log "Installation terminée mais Notepad++ introuvable."
            Write-Host " Erreur : Notepad++ non trouvé après installation."
        }
    } else {
        Write-Log " Échec du téléchargement du fichier d'installation."
        Write-Host " Téléchargement échoué."
    }
}
catch {
    Write-Log " Erreur pendant l'installation : $_"
    Write-Host " Une erreur est survenue : $_"
}
finally {
    Write-Log "Fin du processus d'installation."
}
