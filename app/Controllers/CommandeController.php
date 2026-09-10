<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Services\CommandeService;

class CommandeController extends Controller
{
    public function __construct(private CommandeService $commandeService) {}

    public function index(): string
    {
        $motCle = trim((string) $this->value('q', ''));
        $statut = isset($_GET['statut']) && $_GET['statut'] !== ''
            ? (string) $_GET['statut'] : null;
        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $vue = isset($_GET['vue']) && $_GET['vue'] === 'carte' ? 'carte' : 'liste';

        $resultat = $this->commandeService->lister($motCle !== '' ? $motCle : null, $statut, $page);

        return View::render('commandes/index', [
            'commandes' => $resultat['commandes'],
            'page' => $resultat['page'],
            'totalPages' => $resultat['totalPages'],
            'total' => $resultat['total'],
            'motCle' => $motCle,
            'statut' => $statut ?? '',
            'vue' => $vue,
            'stats' => $this->commandeService->statistiques(),
            'pageTitle' => 'Commandes',
            'baseRoute' => $this->baseRoute(),
        ], 'base');
    }

    public function detail(int $id): string
    {
        $commande = $this->commandeService->trouver($id);

        return View::render('commandes/detail', [
            'commande' => $commande,
            'lignes' => $this->commandeService->lignes($id),
            'paiement' => $this->commandeService->paiement($id),
            'pageTitle' => 'Commande n°' . $id,
            'baseRoute' => $this->baseRoute(),
        ], 'base');
    }

    public function recu(int $id): string
    {
        return View::render('commandes/recu', [
            'commande' => $this->commandeService->trouver($id),
            'lignes' => $this->commandeService->lignes($id),
            'paiement' => $this->commandeService->paiement($id),
            'pageTitle' => 'Reçu n°' . $id,
            'baseRoute' => $this->baseRoute(),
        ], 'base');
    }

    public function recuPdf(int $id): never
    {
        $commande = $this->commandeService->trouver($id);
        $lignes = $this->commandeService->lignes($id);
        $paiement = $this->commandeService->paiement($id);

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($this->htmlRecuPdf($commande, $lignes, $paiement));
        $dompdf->setPaper('A5', 'portrait');
        $dompdf->render();

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="recu-commande-' . $commande->id . '.pdf"');
        header('Cache-Control: no-store');
        echo $dompdf->output();
        exit;
    }

    /**
     * @param \App\Models\LigneCommande[] $lignes
     * @param array<string, mixed>|null $paiement
     */
    private function htmlRecuPdf(
        \App\Models\Commande $commande,
        array $lignes,
        ?array $paiement
    ): string {
        $lignesHtml = '';
        foreach ($lignes as $ligne) {
            $lignesHtml .= '<tr>'
                . '<td>' . htmlspecialchars($ligne->produitLibelle, ENT_QUOTES, 'UTF-8') . '</td>'
                . '<td style="text-align:center;">' . $ligne->quantite . '</td>'
                . '<td style="text-align:right;">' . htmlspecialchars($ligne->prixFormate(), ENT_QUOTES, 'UTF-8') . '</td>'
                . '<td style="text-align:right;">' . htmlspecialchars($ligne->sousTotalFormate(), ENT_QUOTES, 'UTF-8') . '</td>'
                . '</tr>';
        }

        $statut = htmlspecialchars($commande->libelleStatut(), ENT_QUOTES, 'UTF-8');
        $client = htmlspecialchars($commande->clientNomComplet(), ENT_QUOTES, 'UTF-8');
        $paiementHtml = $paiement !== null
            ? 'Payé avec ' . htmlspecialchars((string) $paiement['mode_paiement'], ENT_QUOTES, 'UTF-8')
            : 'Non payé';

        return '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body style="font-family: DejaVu Sans, sans-serif; color:#111827; font-size:12px;">'
            . '<table style="width:100%; border-bottom:1px solid #e5e7eb; padding-bottom:12px;"><tr>'
            . '<td><strong style="font-size:18px;">SAVEUR 221</strong><br/>'
            . '<span style="color:#6b7280;">Restaurant traditionnel sénégalais</span></td>'
            . '<td style="text-align:right;"><strong style="font-size:14px;">Reçu n°' . $commande->id . '</strong><br/>'
            . '<span style="color:#6b7280;">' . htmlspecialchars($commande->dateFormatee(), ENT_QUOTES, 'UTF-8') . '</span></td>'
            . '</tr></table>'
            . '<table style="width:100%; margin:14px 0;"><tr>'
            . '<td><span style="color:#6b7280; font-size:9px;">CLIENT</span><br/><strong>' . $client . '</strong><br/>'
            . ($commande->clientTelephone !== null ? '<span style="color:#6b7280;">' . htmlspecialchars($commande->clientTelephone, ENT_QUOTES, 'UTF-8') . '</span><br/>' : '')
            . ($commande->clientEmail !== null ? '<span style="color:#6b7280;">' . htmlspecialchars($commande->clientEmail, ENT_QUOTES, 'UTF-8') . '</span>' : '')
            . '</td>'
            . '<td style="text-align:right;"><span style="color:#6b7280; font-size:9px;">STATUT</span><br/><strong>' . $statut . '</strong>'
            . '<br/><span style="color:#6b7280; font-size:9px;">PAIEMENT</span><br/><strong>' . $paiementHtml . '</strong></td>'
            . '</tr></table>'
            . '<table style="width:100%; border-collapse:collapse;">'
            . '<tr style="background:#f9fafb; color:#6b7280; font-size:9px; text-transform:uppercase;">'
            . '<th style="text-align:left; padding:6px;">Produit</th>'
            . '<th style="text-align:center; padding:6px;">Qté</th>'
            . '<th style="text-align:right; padding:6px;">Prix unitaire</th>'
            . '<th style="text-align:right; padding:6px;">Sous-total</th></tr>'
            . $lignesHtml
            . '<tr style="border-top:1px solid #e5e7eb;"><td style="padding:8px 6px;"><strong>Total</strong></td>'
            . '<td></td><td></td>'
            . '<td style="text-align:right; color:#d97706; font-weight:bold; font-size:14px;">'
            . htmlspecialchars($commande->montantFormate(), ENT_QUOTES, 'UTF-8') . '</td></tr>'
            . '</table>'
            . '<p style="margin-top:24px; text-align:center; color:#6b7280; font-size:10px;">Merci de votre confiance et bon appétit !</p>'
            . '</body></html>';
    }

    public function changerStatut(int $id): void
    {
        try {
            $statut = (string) ($_POST['statut'] ?? '');
            $this->commandeService->passerAuStatutSuivant($id, $statut);
            flash('success', 'Statut de la commande mis à jour avec succès.');
        } catch (ValidationException $e) {
            flash('error', $e->getMessage());
        } catch (NotFoundException) {
            flash('error', 'Cette commande n\'existe pas.');
        }
        View::redirect($this->redirectionRetour($id));
    }

    public function annuler(int $id): void
    {
        try {
            $this->commandeService->annuler($id);
            flash('success', 'Commande annulée.');
        } catch (ValidationException $e) {
            flash('error', $e->getMessage());
        } catch (NotFoundException) {
            flash('error', 'Cette commande n\'existe pas.');
        }
        View::redirect($this->redirectionRetour($id));
    }

    private function baseRoute(): string
    {
        return ($_SESSION['user']['role'] ?? '') === 'ADMIN' ? '/admin' : '/gerant';
    }

    private function redirectionRetour(int $id): string
    {
        $referer = (string) ($_SERVER['HTTP_REFERER'] ?? '');
        if ($referer !== '' && str_contains($referer, '/commandes/' . $id)) {
            return $this->baseRoute() . '/commandes/' . $id;
        }

        return $this->redirectionListe();
    }

    private function redirectionListe(): string
    {
        $params = $_GET;
        unset($params['_token'], $params['page']);

        return $this->baseRoute() . '/commandes' . ($params ? '?' . http_build_query($params) : '');
    }
}