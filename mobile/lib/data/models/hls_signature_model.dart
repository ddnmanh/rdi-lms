/// Model cho response từ API HLS signature
class HlsSignature {
  final String type;
  final int lessonId;
  final String signedUri;
  final String signature;
  final int expires;
  final String expiresAt;
  final String baseUrl;
  final String uri;

  HlsSignature({
    required this.type,
    required this.lessonId,
    required this.signedUri,
    required this.signature,
    required this.expires,
    required this.expiresAt,
    required this.baseUrl,
    required this.uri,
  });

  factory HlsSignature.fromJson(Map<String, dynamic> json) {
    return HlsSignature(
      type: json['type'] ?? 'hls',
      lessonId: json['lesson_id'] ?? 0,
      signedUri: json['signed_uri'] ?? '',
      signature: json['signature'] ?? '',
      expires: json['expires'] ?? 0,
      expiresAt: json['expires_at'] ?? '',
      baseUrl: json['base_url'] ?? '',
      uri: json['uri'] ?? '',
    );
  }

  /// Lấy URL đầy đủ để stream video HLS
  String get fullStreamUrl => '$baseUrl$signedUri';

  /// Kiểm tra xem signature đã hết hạn chưa
  bool get isExpired {
    final now = DateTime.now().millisecondsSinceEpoch ~/ 1000;
    return now >= expires;
  }
}

