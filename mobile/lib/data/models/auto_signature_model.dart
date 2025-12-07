/// Model cho response từ API auto signature (hỗ trợ cả HLS và MP4)
class AutoSignature {
  final String type;
  final int lessonId;
  final String signedUri;
  final String signature;
  final int expires;
  final String expiresAt;
  final String baseUrl;
  final String uri;

  AutoSignature({
    required this.type,
    required this.lessonId,
    required this.signedUri,
    required this.signature,
    required this.expires,
    required this.expiresAt,
    required this.baseUrl,
    required this.uri,
  });

  factory AutoSignature.fromJson(Map<String, dynamic> json) {
    return AutoSignature(
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

  /// Lấy URL đầy đủ để stream video (hỗ trợ cả HLS và MP4)
  String get fullStreamUrl => '$baseUrl$signedUri';
  
  /// Kiểm tra xem có phải video HLS không
  bool get isHls => type.toLowerCase() == 'hls';
  
  /// Kiểm tra xem có phải video MP4 không  
  bool get isMp4 => type.toLowerCase() == 'mp4';

  /// Kiểm tra xem signature đã hết hạn chưa
  bool get isExpired {
    final now = DateTime.now().millisecondsSinceEpoch ~/ 1000;
    return now >= expires;
  }
}

